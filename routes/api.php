<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiscountTypeController;
use App\Http\Controllers\CategoryContorller;

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
Route::post('/reset-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //****************   USER CONTOLLER ******************
    Route::post('/user/add', [UserController::class, 'add']);
    Route::put('/user/update/{id}', [UserController::class, 'update']);
    Route::post('/user/getAll', [UserController::class, 'getAll']);
    Route::post('/user/getAllWithRelations', [
        UserController::class,
        'getAllWithRelations',
    ]);
    Route::get('/user/getRoles', [UserController::class, 'getRoles']);
    Route::post('/user/getCurrentUserData', [
        UserController::class,
        'getCurrentUserData',
    ]);
    Route::post('/user/changeStatus', [UserController::class, 'changeStatus']);

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
});
