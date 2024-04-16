<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiscountTypeController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //****************   USER CONTOLLER ******************
    Route::post('/user/add', [UserController::class, 'add']);
    Route::post('/user/getAll', [UserController::class, 'getAll']);
    Route::post('/user/getCurrentUserData', [UserController::class, 'getCurrentUserData']);

    //****************   DISCOUNT TYPE CONTOLLER ******************
    Route::get('/discountType/getAll', [DiscountTypeController::class, 'getAll']);
    Route::post('/discountType/add', [DiscountTypeController::class, 'add']);
    Route::put('/discountType/update/{id}', [DiscountTypeController::class, 'update']);
    Route::delete('/discountType/delete/{id}', [DiscountTypeController::class, 'delete']);
});
