<?php

use App\Http\Controllers\AdController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryFieldController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CarModelController;
use App\Http\Controllers\GoogleController;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('front.pages.home');
});

Auth::routes();

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){

        Route::group(['middleware' => 'guest'],function(){
            // Route::get('/', function () {
            //     return view('front.pages.home');
            // });
        });
            Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
            Route::resource('banners',BannerController::class);
            Route::resource('categorys',CategoryController::class);
            Route::resource('countrys',CountryController::class);
            Route::resource('citys',CityController::class);
                  Route::resource('blogs',BlogController::class);

                  Route::get('/car-models', [CarModelController::class, 'index'])->name('carModel.index');

                  // Route to delete a car model
Route::delete('/car-models/{id}', [CarModelController::class, 'destroy'])->name('carModel.delete');

 Route::delete('/category-field-values/{valueId}/delete', [CategoryFieldController::class, 'deleteValue']);
            Route::get('/categories/{id}/fields/create', [CategoryFieldController::class, 'create'])->name('categories.fields.create'); // إضافة
            Route::post('/categories/{id}/fields/store', [CategoryFieldController::class, 'store'])->name('categories.fields.store'); // تخزين
            Route::get('/categories/{id}/fields', [CategoryFieldController::class, 'show'])->name('categories.fields.show');

// إضافة مسار لحفظ الموديلات
Route::post('/categories/{category}/fields/store-car-model', [CategoryFieldController::class, 'storeCarModel'])
    ->name('categories.fields.store-car-model');

            Route::get('/categories/{id}/fields/{field_id}/edit', [CategoryFieldController::class, 'edit'])->name('categories.fields.edit'); // تعديل
            Route::post('/categories/{id}/fields/{field_id}/update', [CategoryFieldController::class, 'update'])->name('categories.fields.update'); // تحديث

            Route::delete('/categories/{id}/fields/{field_id}/destroy', [CategoryFieldController::class, 'destroy'])->name('categories.fields.destroy'); // حذف
            Route::get('/ads-management', [AdController::class, 'index'])->name('ads.management');
            Route::put('/ads-management/{id}', [AdController::class, 'updateStatus'])->name('ads.updateStatus');
            Route::delete('/ads/{id}', [AdController::class, 'destroy'])->name('ads.destroy');
      
      
      Route::post('categories/{category}/fields/ensureExists', [CategoryFieldController::class, 'ensureExists'])->name('categories.fields.ensureExists');

Route::post('/categories/{categoryId}/toggle-kilometers', [CategoryController::class, 'toggleKilometers'])->name('categories.toggleKilometers');

// Test routes for Pusher
Route::get('/pusher-test', function () {
    return view('pusher-test');
})->name('pusher.test');

Route::get('/pusher-test-arabic', function () {
    return view('pusher-test-arabic');
})->name('pusher.test.arabic');

Route::post('/trigger-test-event', function () {
    $message = request('message', 'Hello from Pusher! Time: ' . now()->format('H:i:s'));
    event(new App\Events\AuctionUpdateTest($message));
    return response()->json(['status' => 'Event triggered', 'message' => $message]);
})->name('trigger.test.event');

Route::get('auth/google',[GoogleController::class,'googlepage']);
Route::get('auth/google/callback',[GoogleController::class,'googlecallback']);
Route::get('/google/authenticate', [GoogleController::class, 'getLogindataUsingGoogleCode']);

// Public pages
Route::get('/privacy-policy', function () {
    return view('front.pages.privacy-policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('front.pages.terms-of-service');
})->name('terms.service');

// Serve Google Search Console verification file (in case static file isn't served by webserver)
Route::get('/google7afffc6b0c14f7cf.html', function () {
    return response('google-site-verification: google7afffc6b0c14f7cf.html', 200)
        ->header('Content-Type', 'text/plain');
});

});