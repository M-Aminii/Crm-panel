<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\Glass\LayerController;
use App\Http\Controllers\GlassFinalStructureController;
use App\Http\Controllers\IndividualCustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Models\Province;
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

Route::get('/provinces', function () {
    $provinces = Province::all();
    return response()->json($provinces);
});

Route::get('/provinces/{id}/cities', function ($id) {
    $province = Province::findOrFail($id);
    $cities = $province->cities;
    return response()->json($cities);
});

Route::group(["middleware" => ['role:super-admin',"auth:api"],'prefix' => 'Admin'], function () {

    Route::group(['prefix' => '/user'], function () {
        Route::post('/',[UserController::class ,'store']);
        Route::get('/list',[UserController::class ,'index']);
        Route::get('/{id}',[UserController::class ,'show']);
        Route::patch('/{id}',[UserController::class ,'update']);
    });

    Route::group(['prefix' => '/customer'], function () {
        Route::post('/',[CustomerController::class ,'store']);
        Route::get('/list',[CustomerController::class ,'index']);
        Route::get('/{id}',[CustomerController::class ,'show']);
        Route::patch('/{id}',[CustomerController::class ,'update']);

    });
    Route::group(['prefix' => '/glass-structure'], function () {
            Route::post('/',[GlassFinalStructureController::class ,'store']);
           // Route::get('/list',[IndividualCustomerController::class ,'index']);
            Route::get('/{id}',[GlassFinalStructureController::class ,'show']);
           // Route::patch('/{id}',[IndividualCustomerController::class ,'update']);
        });

    Route::group(['prefix' => '/invoice'], function () {
        Route::post('/',[InvoiceController::class ,'store']);
        Route::get('/',[InvoiceController::class ,'index']);
        Route::get('/{id}',[InvoiceController::class ,'show']);
        Route::get('/download/{id}',[InvoiceController::class ,'download']);
        Route::patch('/{id}',[InvoiceController::class ,'update']);
        Route::delete('/{id}',[InvoiceController::class ,'destroy']);

    });
   /* Route::group(['prefix' => '/glass-layer'], function () {
        Route::post('/',[LayerController::class ,'store']);
        //Route::get('/list',[IndividualCustomerController::class ,'index']);
       // Route::get('/{id}',[IndividualCustomerController::class ,'show']);
        Route::patch('/{id}',[LayerController::class ,'update']);
    });*/




});


/*Route::group(["middleware" => ['role:super-admin',"auth:api"],'prefix' => 'Admin'], function () {

    Route::get('/user',[LegalCustomerController::class ,'index']);
    Route::delete('/user/{user_id}',[LegalCustomerController::class ,'delete']);
    Route::put('/user/{user_id}',[LegalCustomerController::class ,'update']);

});*/
