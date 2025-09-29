<?php

namespace App\Http\Controllers\Api;

use App\Models\Ad;
use App\Models\AuctionHandler;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuctionController extends Controller
{
    /**
     * إنشاء مزاد جديد
     * يستخدم AdController::store() أولاً ثم يضيف بيانات المزاد
     */
    public function createAuction(Request $request)
    {
        // التحقق من صحة بيانات المزاد
        $validator = Validator::make($request->all(), [
            'starting_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'auto_extend' => 'nullable|boolean',
            'extend_minutes' => 'nullable|integer|min:1',
            'bid_increment' => 'nullable|numeric|min:1',
            // جميع حقول الإعلان العادي أيضاً مطلوبة
            'category_id' => 'required|exists:categories,id',
            'country_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'price' => 'required|numeric',
            'main_image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // الخطوة 1: إنشاء الإعلان باستخدام AdController
        $adController = new \App\Http\Controllers\Api\AdController();
        
        // تعديل البيانات لتتوافق مع متطلبات AdController
        $adRequest = new Request();
        $adRequest->merge($request->all());
        
        // استدعاء دالة store من AdController
        $adResponse = $adController->store($adRequest);
        
        // الخطوة 2: استخراج معرف الإعلان من الاستجابة باستخدام الطريقة المقترحة
        $adData = $adResponse->getData(true);
        
        // التحقق من وجود بيانات الإعلان
        if (!isset($adData['ad']['id'])) {
            return response()->json([
                'error' => 'فشل في إنشاء الإعلان',
                'ad_response' => $adData
            ], 500);
        }
        
        $adId = $adData['ad']['id'];
        
        // الخطوة 3: تحديث حالة الإعلان إلى "auction"
        $ad = Ad::find($adId);
        if ($ad) {
            $ad->status = 'auction';
            $ad->save();
        }
        
        // الخطوة 4: إنشاء بيانات المزاد
        try {
            $auction = AuctionHandler::create([
                'ad_id' => $adId,
                'user_id' => auth()->id(),
                'starting_price' => $request->starting_price,
                'current_price' => $request->starting_price,
                'reserve_price' => $request->reserve_price,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'pending', // في انتظار الموافقة
                'auto_extend' => $request->auto_extend ?? false,
                'extend_minutes' => $request->extend_minutes ?? 5,
                'bid_increment' => $request->bid_increment ?? 10,
                'total_bids' => 0,
            ]);

            return response()->json([
                'message' => 'تم إنشاء المزاد بنجاح',
                'ad_id' => $adId,
                'auction_id' => $auction->id,
                'ad_data' => $adData['ad'],
                'auction_data' => $auction
            ], 201);

        } catch (\Exception $e) {
            // في حالة فشل إنشاء المزاد، نحذف الإعلان
            if ($ad) {
                $ad->delete();
            }
            
            return response()->json([
                'error' => 'فشل في إنشاء بيانات المزاد',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تفاصيل مزاد
     */
    public function show($auctionId)
    {
        $auction = AuctionHandler::with(['ad', 'bids.user'])
            ->find($auctionId);

        if (!$auction) {
            return response()->json(['message' => 'المزاد غير موجود'], 404);
        }

        return response()->json([
            'auction' => $auction,
            'remaining_time' => $this->getRemainingTime($auction->end_time),
            'is_active' => $this->isAuctionActive($auction)
        ]);
    }

    /**
     * وضع مزايدة جديدة
     */
    public function placeBid(Request $request, $auctionId)
    {
        $validator = Validator::make($request->all(), [
            'bid_amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $auction = AuctionHandler::find($auctionId);
        if (!$auction) {
            return response()->json(['message' => 'المزاد غير موجود'], 404);
        }

        // التحقق من أن المزاد نشط
        if (!$this->isAuctionActive($auction)) {
            return response()->json(['message' => 'المزاد غير نشط'], 400);
        }

        $bidAmount = $request->bid_amount;
        $userId = auth()->id();

        // التحقق من أن المستخدم ليس صاحب المزاد
        if ($auction->user_id == $userId) {
            return response()->json(['message' => 'لا يمكنك المزايدة على مزادك الخاص'], 400);
        }

        // التحقق من أن المبلغ أكبر من السعر الحالي + الحد الأدنى للزيادة
        $minimumBid = $auction->current_price + $auction->bid_increment;
        if ($bidAmount < $minimumBid) {
            return response()->json([
                'message' => "المبلغ يجب أن يكون على الأقل {$minimumBid}",
                'minimum_bid' => $minimumBid
            ], 400);
        }

        try {
            // إنشاء المزايدة
            $bid = AuctionBid::create([
                'auction_id' => $auctionId,
                'user_id' => $userId,
                'bid_amount' => $bidAmount,
                'bid_time' => now()
            ]);

            // تحديث السعر الحالي وعدد المزايدات
            $auction->current_price = $bidAmount;
            $auction->total_bids += 1;
            $auction->save();

            return response()->json([
                'message' => 'تم وضع المزايدة بنجاح',
                'bid' => $bid,
                'new_current_price' => $bidAmount
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في وضع المزايدة',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض جميع المزادات النشطة
     */
    public function activeAuctions(Request $request)
    {
        $query = AuctionHandler::with(['ad', 'bids'])
            ->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());

        // فلترة حسب التصنيف
        if ($request->has('category_id')) {
            $query->whereHas('ad', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // فلترة حسب الدولة
        if ($request->has('country_id')) {
            $query->whereHas('ad', function($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        $auctions = $query->orderBy('end_time', 'asc')->get();

        $auctions->transform(function($auction) {
            $auction->remaining_time = $this->getRemainingTime($auction->end_time);
            return $auction;
        });

        return response()->json(['auctions' => $auctions]);
    }

    /**
     * حساب الوقت المتبقي للمزاد
     */
    private function getRemainingTime($endTime)
    {
        $now = Carbon::now();
        $end = Carbon::parse($endTime);
        
        if ($end->isPast()) {
            return ['expired' => true];
        }

        $diff = $now->diff($end);
        
        return [
            'expired' => false,
            'days' => $diff->days,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
            'total_seconds' => $now->diffInSeconds($end)
        ];
    }

    /**
     * التحقق من أن المزاد نشط
     */
    private function isAuctionActive($auction)
    {
        $now = Carbon::now();
        return $auction->status === 'active' && 
               $now->greaterThanOrEqualTo($auction->start_time) && 
               $now->lessThanOrEqualTo($auction->end_time);
    }
}