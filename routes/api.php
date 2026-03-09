<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CalculateController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\SheetMetalPriceController;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\Hazirlayan;
use App\Models\PaymentExtra;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum', 'admin']], function () {
    Route::post('/upload', function (Request $request) {
        $exists = Image::where('modul_sayisi_boy', $request->modul_sayisi_boy)
            ->where('modul_sayisi_en', $request->modul_sayisi_en)
            ->where('yukseklik', $request->yukseklik)
            ->where('aciklama', $request->aciklama)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Bu varyasyon zaten mevcut.'
            ], 400);
        }
        $file = $request->file('file');
        if ($file) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '-' . uniqid() . '.' . $extension;
            $filePath = Storage::putFileAs('public/images', $file, $fileName);
            $fileUrl = Storage::url($filePath);
            Image::create([
                'modul_sayisi_boy' => $request->modul_sayisi_boy,
                'modul_sayisi_en' => $request->modul_sayisi_en,
                'yukseklik' => $request->yukseklik,
                'image' => $fileUrl,
                'aciklama' => $request->aciklama
            ]);
            return response()->json([
                'message' => "Resim başarıyla eklendi!"
            ]);
        } else {
            return response()->json([
                'message' => "Resim eklenemedi!"
            ], 400);
        }
    });
    Route::post('/label1/price', [PaymentController::class, 'label1']);
    Route::post('/label2/price', [PaymentController::class, 'label2']);
    Route::get('/label1/price', [PaymentController::class, 'getLabel1']);
    Route::get('/label2/price', [PaymentController::class, 'getLabel2']);
    Route::post('/update/sheet-metal-prices', [SheetMetalPriceController::class, 'update']);
    Route::delete('author/{id}', function ($id) {
        Hazirlayan::find($id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Hazırlayan başarıyla silindi!'
        ]);
    });
    Route::post('/author', function (Request $request) {
        $authName = $request->auth_name;
        $phone = $request->phone;
        $email = $request->email;
        Hazirlayan::create([
            'auth_name' => $authName,
            'phone' => $phone ? $phone : null,
            'email' => $email ? $email : null,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Hazırlayan başarıyla eklendi!'
        ]);
    });
});
Route::prefix('v1')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::get('cities', function () {
        $cities = City::all();
        return response()->json([
            'status' => true,
            'message' => 'İller başarıyla getirildi!',
            'cities' => $cities
        ]);
    });
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/customer/search', [CustomerController::class, 'searchCustomer']);
    Route::post('/search/author', function (Request $request) {
        $search = $request->search;
        $authors = Hazirlayan::whereRaw("MATCH(auth_name) AGAINST (? IN NATURAL LANGUAGE MODE)", [$search])
            ->orWhere('auth_name', 'like', '%' . $search . '%')
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Hazırlayanlar başarıyla getirildi!',
            'authors' => $authors
        ]);
    });
    Route::get('/customers', [CustomerController::class, 'customers']);
    Route::delete('/customer/delete/{id}', [CustomerController::class, 'delete']);
    Route::put('/customer/update/{id}', [CustomerController::class, 'newUpdate']);
    Route::post('/customer/add', [CustomerController::class, 'add']);
    Route::get('/get/sheet-metal-prices', [SheetMetalPriceController::class, 'prices']);
    Route::post('/calculate', [CalculateController::class, 'newCalculate']);
    Route::post('/calculate/update', [CalculateController::class, 'newUpdate']);
    Route::get('/offers', [CalculateController::class, 'offers']);
    Route::get('/offer/{id}', [CalculateController::class, 'offerById']);
    Route::get('/products', function () {
        return Product::all();
    });
    Route::get('/authors', function (Request $request) {
        $authors = Hazirlayan::all();
        return response()->json([
            'status' => true,
            'message' => 'Hazırlayanlar listesi başarıyla getirlidi!',
            'authors' => $authors
        ]);
    });
    Route::get('/rates', function (Request $request) {
        $rates = PaymentExtra::all();
        return response()->json([
            'status' => true,
            'message' => 'Ödeme ekstraları başarıyla getirildi!',
            'rates' => $rates
        ]);
    });
    Route::post('/rate', function (Request $request) {
        $paymentExtra = PaymentExtra::find(1);

        PaymentExtra::updateOrCreate(
            ['id' => 1],
            [
                'tax' => $request->tax ?? $paymentExtra->tax,
                'vade' => $request->vade ?? $paymentExtra->vade,
                'dolar_kuru' => $request->dolar_kuru ?? $paymentExtra->dolar_kuru
            ]
        );
        return response()->json([
            'status' => true,
            'message' => 'Ödeme ekstraları başarıyla güncellendi!'
        ]);
    });
    Route::get('/images', [ImageController::class, 'images']);
    Route::delete('/image/{id}', [ImageController::class, 'delete']);
});
Route::get('v1/check/token', function () {
    if (Auth::check()) {
        return response()->json(['status' => true, 'message' => 'Token is valid'], 200);
    } else {
        return response()->json(['status' => false, 'message' => 'Token is invalid or expired'], 401);
    }
})->middleware('auth:sanctum');
