# Auction APIs Documentation

## 📋 Overview
مجموعة APIs كاملة لإدارة نظام المزادات في تطبيق Motors.

## 🚀 Available APIs

### 1. Authentication
- **POST** `/api/login` - تسجيل الدخول والحصول على JWT Token

### 2. Auction Management
- **POST** `/api/auctions/create` - إنشاء مزاد جديد (يتطلب تسجيل دخول)
- **PATCH** `/api/auctions/{id}/activate` - تفعيل المزاد (يتطلب تسجيل دخول)
- **GET** `/api/auctions/active` - جلب المزادات النشطة (عام)
- **GET** `/api/auctions/{id}` - جلب تفاصيل مزاد محدد (عام)
- **POST** `/api/auctions/{id}/bid` - وضع مزايدة (يتطلب تسجيل دخول)

## 📁 Postman Collections

### Files Available:
1. **`Auction_API_Test.postman_collection.json`** - المجموعة الأصلية المحدثة
2. **`Complete_Auction_APIs.postman_collection.json`** - مجموعة كاملة مع جميع APIs
3. **`Auction_Environment.postman_environment.json`** - بيئة العمل للمتغيرات

## 🔧 Setup Instructions

### 1. Import Collections
1. افتح Postman
2. اضغط على "Import"
3. اختر الملفات:
   - `Complete_Auction_APIs.postman_collection.json`
   - `Auction_Environment.postman_environment.json`

### 2. Configure Environment
1. اختر "Auction APIs Environment" من القائمة المنسدلة
2. تأكد من أن `base_url` محدد بشكل صحيح
3. سيتم تعبئة `jwt_token` تلقائياً عند تسجيل الدخول

### 3. Test Flow
1. **Login** - احصل على JWT Token
2. **Create Auction** - أنشئ مزاد جديد
3. **Activate Auction** - فعّل المزاد
4. **Get Active Auctions** - تحقق من المزادات النشطة
5. **Place Bid** - ضع مزايدة
6. **Get Auction Details** - اطلع على تفاصيل المزاد

## 📊 Current Database Status

### Active Auctions:
- Auction ID: 19 (Ad ID: 557) - Status: active
- Auction ID: 20 (Ad ID: 558) - Status: active

### Test Data:
```json
{
  "auction_id": "19",
  "ad_id": "557",
  "starting_price": "1800.00",
  "current_highest_bid": "1800.00",
  "status": "active"
}
```

## 🔐 Authentication

### JWT Token Required for:
- Creating auctions
- Activating auctions
- Placing bids

### Public Endpoints:
- Getting active auctions
- Getting auction details

## 📝 Example Requests

### Activate Auction
```bash
PATCH /api/auctions/19/activate
Headers:
  Authorization: Bearer {jwt_token}
  Content-Type: application/json
  Accept: application/json
```

### Response:
```json
{
  "status": true,
  "message": "تم تفعيل المزاد بنجاح",
  "auction": {
    "id": 19,
    "ad_id": 557,
    "status": "active",
    "starting_price": "1800.00",
    "current_highest_bid": "1800.00",
    "start_time": "2025-09-29 10:00:00",
    "end_time": "2025-10-01 18:00:00",
    "remaining_time": {...},
    "is_active": true
  }
}
```

## ⚠️ Important Notes

1. **Environment Variables**: تأكد من تحديث المتغيرات في Environment
2. **JWT Token**: سيتم حفظ التوكن تلقائياً عند تسجيل الدخول الناجح
3. **File Uploads**: عند إنشاء مزاد، تأكد من رفع صورة رئيسية
4. **Auction Status**: المزادات تُنشأ بحالة `pending` وتحتاج تفعيل لتصبح `active`

## 🎯 Testing Strategy

1. **بداية الاختبار**: ابدأ بـ Login للحصول على التوكن
2. **إنشاء المزاد**: استخدم Create Auction مع البيانات المطلوبة
3. **التفعيل**: فعّل المزاد باستخدام Activate Auction
4. **المزايدة**: اختبر وضع مزايدات مختلفة
5. **التحقق**: استخدم Get APIs للتحقق من النتائج

## 📞 Support
لأي استفسارات أو مشاكل في الاختبار، تحقق من:
- Console logs في Postman
- Response status codes
- Error messages في الاستجابات