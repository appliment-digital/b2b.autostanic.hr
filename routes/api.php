<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiscountTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BitrixController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDetailController;
use App\Http\Controllers\WarrentController;
use App\Http\Controllers\DeliveryDeadlineController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//****************  AUTH CONTOLLER  ******************

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

//****************   CATEGORY CONTOLLER ******************

Route::get('/category/categories', [
    CategoryController::class,
    'getMainCategories',
]);

Route::get('/test', function (Request $Request) {
    return response()->json([
        'x' => 'hi',
    ]);
});

Route::get('/user/getCurrentUserData', [
    UserController::class,
    'getCurrentUserData',
]);

//**************** MIDDLEWARE ******************

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //****************   USER CONTOLLER ******************

    Route::get('/user/getRoles', [UserController::class, 'getRoles']);
    Route::post('/user/add', [UserController::class, 'add']);
    Route::post('/user/getAll', [UserController::class, 'getAll']);
    Route::post('/user/changeStatus', [UserController::class, 'changeStatus']);
    Route::post('/user/getAllWithRelations', [
        UserController::class,
        'getAllWithRelations',
    ]);

    Route::put('/user/update/{id}', [UserController::class, 'update']);

    //****************   DISCOUNT TYPE CONTOLLER ******************

    Route::get('/discountType/getAll', [
        DiscountTypeController::class,
        'getAll',
    ]);
    Route::post('/discountType/add', [DiscountTypeController::class, 'add']);
    Route::put('/discountType/update/{id}', [
        DiscountTypeController::class,
        'update',
    ]);
    Route::delete('/discountType/delete/{id}', [
        DiscountTypeController::class,
        'delete',
    ]);

    //****************   CATEGORY CONTOLLER ******************

    Route::get('/category/subcategories/{id}', [
        CategoryController::class,
        'getSubcategories',
    ]);

    //****************   PRODUCT CONTOLLER ******************

    Route::post(
        '/product/getProductsByCategoryId/{categoryId}/{page}/{pageSize}',
        [ProductController::class, 'getProductsByCategoryId']
    );
    Route::get('/product/getProductById/{id}', [
        ProductController::class,
        'getProductById',
    ]);
    Route::get('/product/getProductPictures/{id}', [
        ProductController::class,
        'getProductPictures',
    ]);
    Route::get('/product/getOEMCodeForProduct/{id}', [
        ProductController::class,
        'getOEMCodeForProduct',
    ]);
    Route::get('/product/getCarTypesForProduct/{id}', [
        ProductController::class,
        'getCarTypesForProduct',
    ]);
    Route::get('/product/getSpecificationAttributeForProduct/{id}', [
        ProductController::class,
        'getSpecificationAttributeForProduct',
    ]);
    Route::get('/product/getProductDetails/{id}', [
        ProductController::class,
        'getProductDetails',
    ]);
    Route::post('/product/getProductsBySupplierCategoresAndPriceRange', [
        ProductController::class,
        'getProductsBySupplierCategoresAndPriceRange',
    ]);

    //****************   BITRIX CONTOLLER ******************

    Route::post('/bitrix/getCountriesList', [
        BitrixController::class,
        'getCountriesList',
    ]);

    //****************   Supplier CONTOLLER ******************

    Route::post('/supplier/getAll', [SupplierController::class, 'getAll']);
    Route::get('/supplier/getCategoriesForSupplier/{id}', [
        SupplierController::class,
        'getCategoriesForSupplier',
    ]);

    //****************   Supplier Detail CONTOLLER ******************

    Route::get('/supplierDetail/getSupplierWithDetails/{id}', [
        SupplierDetailController::class,
        'getSupplierWithDetails',
    ]);

    Route::get('/supplierDetail/getCategoryName/{id}', [
        SupplierDetailController::class,
        'getCategoryName',
    ]);

    Route::get('/supplierDetail/getProductName/{id}', [
        SupplierDetailController::class,
        'getProductName',
    ]);

    Route::get('/supplierDetail/getAllSuppliersWithDetails', [
        SupplierDetailController::class,
        'getAllSuppliersWithDetails',
    ]);

    Route::get('/supplierDetail/getUniqueCategories', [
        SupplierDetailController::class,
        'getUniqueCategories',
    ]);

    Route::post('/supplierDetail/getAddedPriceRange', [
        SupplierDetailController::class,
        'getAddedPriceRange',
    ]);

    Route::post('/supplierDetail/addDetailsforSupplier', [
        SupplierDetailController::class,
        'addDetailsforSupplier',
    ]);

    Route::put('/supplierDetail/updateDetailsforSupplier/{id}', [
        SupplierDetailController::class,
        'updateDetailsforSupplier',
    ]);

    //****************   Warrant CONTOLLER ******************

    Route::get('/warrent/getAll', [WarrentController::class, 'getAll']);

    //****************   DeliveryDeadline CONTOLLER ******************

    Route::get('/deliveryDeadline/getAll', [
        DeliveryDeadlineController::class,
        'getAll',
    ]);
});
