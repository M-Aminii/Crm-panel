<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\Glass\Layer;
use App\Http\Controllers\IndividualCustomerController;
use App\Http\Controllers\LegalCustomerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class ,'login']);
    Route::post('logout', [AuthController::class ,'logout']);
    Route::post('refresh', [AuthController::class ,'refresh']);
    Route::post('me', [AuthController::class ,'me']);
});

Route::group(["middleware" => ['role:super-admin',"auth:api"],'prefix' => 'Admin'], function () {

    Route::group(['prefix' => '/user'], function () {
        Route::post('/',[UserController::class ,'store']);
        Route::get('/list',[UserController::class ,'index']);
        Route::get('/{id}',[UserController::class ,'show']);
        Route::patch('/{id}',[UserController::class ,'update']);
    });

    Route::group(['prefix' => '/legal-customer'], function () {
        Route::post('/',[LegalCustomerController::class ,'store']);
        Route::get('/list',[LegalCustomerController::class ,'index']);
        Route::get('/{id}',[LegalCustomerController::class ,'show']);
        Route::patch('/{id}',[LegalCustomerController::class ,'update']);

    });

    Route::group(['prefix' => '/individual-customer'], function () {
        Route::post('/',[IndividualCustomerController::class ,'store']);
        Route::get('/list',[IndividualCustomerController::class ,'index']);
        Route::get('/{id}',[IndividualCustomerController::class ,'show']);
        Route::patch('/{id}',[IndividualCustomerController::class ,'update']);
    });

    Route::group(['prefix' => '/glass-layer'], function () {
        Route::post('/',[Layer::class ,'store']);
       /* Route::get('/list',[IndividualCustomerController::class ,'index']);
        Route::get('/{id}',[IndividualCustomerController::class ,'show']);
        Route::patch('/{id}',[IndividualCustomerController::class ,'update']);*/
    });




});

/*Route::group(["middleware" => ['role:super-admin',"auth:api"],'prefix' => 'Admin'], function () {

    Route::get('/user',[LegalCustomerController::class ,'index']);
    Route::delete('/user/{user_id}',[LegalCustomerController::class ,'delete']);
    Route::put('/user/{user_id}',[LegalCustomerController::class ,'update']);

});*/
