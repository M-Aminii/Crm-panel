<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\DescriptionDimensionController;
use App\Http\Controllers\Glass\LayerController;
use App\Http\Controllers\GlassFinalStructureController;
use App\Http\Controllers\IndividualCustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDiscountController;
use App\Models\Province;
use App\Models\UserDiscount;
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

/*Route::group(["middleware" => ['role:super-admin',"auth:api"],'prefix' => 'Admin'], function () {

    Route::group(['prefix' => '/user'], function () {
        Route::post('/',[UserController::class ,'store']);
        Route::get('/list',[UserController::class ,'index']);
        Route::get('/{id}',[UserController::class ,'show']);
        Route::patch('/{id}',[UserController::class ,'update']);
    });

    Route::group(['prefix' => '/customer'], function () {
        Route::post('/',[CustomerController::class ,'store']);
        Route::get('/list',[CustomerController::class ,'index']);
        Route::get('/{customer}',[CustomerController::class ,'show']);
        Route::patch('/{customer}',[CustomerController::class ,'update']);

    });


    Route::group(['prefix' => '/invoice'], function () {
        Route::post('/',[InvoiceController::class ,'store']);
        Route::get('/',[InvoiceController::class ,'index']);
        Route::get('/{invoice}',[InvoiceController::class ,'show']);
        Route::patch('/{invoice}',[InvoiceController::class ,'update']);
        Route::delete('/{invoice}',[InvoiceController::class ,'destroy']);
    });
});*/


Route::group(['middleware' => ['auth:api'], 'prefix' => 'Admin'], function () {
    Route::group(['prefix' => '/user'], function () {
        Route::post('/', [UserController::class, 'store'])->middleware('permission:manage users');
        Route::get('/list', [UserController::class, 'index'])->middleware('permission:view users');
        Route::get('/{id}', [UserController::class, 'show'])->middleware('permission:view users');
        Route::patch('/{id}', [UserController::class, 'update'])->middleware('permission:manage users');
    });

    Route::group(['prefix' => '/customer'], function () {
        Route::post('/', [CustomerController::class, 'store'])->middleware('permission:manage customers');
        Route::get('/list', [CustomerController::class, 'index'])->middleware(['permission:view customers', 'check.customers']);
        Route::get('/{customer}', [CustomerController::class, 'show'])->middleware(['permission:view customers', 'check.customers']);
        Route::patch('/{customer}', [CustomerController::class, 'update'])->middleware(['permission:manage customers', 'check.customers']);
    });

    Route::group(['prefix' => '/invoice'], function () {
        Route::post('/', [InvoiceController::class, 'store'])->middleware('permission:manage invoices');
        Route::get('/', [InvoiceController::class, 'index'])->middleware('permission:view invoices','check.invoices');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:view invoices','check.invoices');
        Route::patch('/{invoice}', [InvoiceController::class, 'update'])->middleware('permission:manage invoices', 'check.invoices');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->middleware('permission:manage invoices' ,'check.invoices');
    });


    Route::group(['prefix' => '/description_dimension'], function () {
        Route::post('/', [DescriptionDimensionController::class, 'store'])->middleware('permission:manage description_dimension');
        Route::get('/', [DescriptionDimensionController::class, 'index'])->middleware('permission:view description_dimension');
        Route::get('/{id}', [DescriptionDimensionController::class, 'show'])->middleware('permission:view description_dimension');
        Route::patch('/{id}', [DescriptionDimensionController::class, 'update'])->middleware('permission:manage description_dimension');
        Route::delete('/{id}', [DescriptionDimensionController::class, 'destroy'])->middleware('permission:manage description_dimension');
    });

    Route::group(['prefix' => '/user_discount'], function () {
        Route::post('/', [UserDiscountController::class, 'store'])->middleware('permission:manage user_discount');
        Route::get('/', [UserDiscountController::class, 'index'])->middleware('permission:view user_discount');
        Route::get('/{id}', [UserDiscountController::class, 'show'])->middleware('permission:view user_discount');
        Route::patch('/{id}', [UserDiscountController::class, 'update'])->middleware('permission:manage user_discount');
        Route::delete('/{id}', [UserDiscountController::class, 'destroy'])->middleware('permission:manage user_discount');
    });
});


/*Route::group(["middleware" => ['role:member',"auth:api"],'prefix' => 'Admin'], function () {

    Route::group(['prefix' => '/customer'], function () {
        Route::post('/',[CustomerController::class ,'store']);
        Route::get('/list',[CustomerController::class ,'index']);
        Route::get('/{id}',[CustomerController::class ,'show']);
        Route::patch('/{id}',[CustomerController::class ,'update']);

    });


    Route::group(['prefix' => '/invoice'], function () {
        Route::post('/',[InvoiceController::class ,'store']);
        Route::get('/',[InvoiceController::class ,'index']);
        Route::get('/{invoice}',[InvoiceController::class ,'show']);
        Route::patch('/{invoice}',[InvoiceController::class ,'update']);
        Route::delete('/{invoice}',[InvoiceController::class ,'destroy']);
    });





});*/
