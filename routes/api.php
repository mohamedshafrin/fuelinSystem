<?php

use Illuminate\Http\Request;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Route;

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

//Public Auth Controller
Route::post('/user_login',[App\Http\Controllers\API\AuthController::class, 'userLogin'])->name('api.auth.user_login');
Route::post('/customer_login',[App\Http\Controllers\API\AuthController::class, 'customerLogin'])->name('api.auth.customer_login');
Route::post('/customer_register',[App\Http\Controllers\API\AuthController::class, 'customer_register'])->name('api.auth.customer_register');

//Fuel Station
Route::middleware(['jwt_verify'])->group(function () {

    //Station User
    Route::middleware(['jwt_user'])->group(function () {
        Route::post('/user_logout',[App\Http\Controllers\API\AuthController::class, 'user_logout'])->name('api.auth.user_logout');
        Route::get('/user_profile',[App\Http\Controllers\API\AuthController::class, 'user_profile'])->name('api.auth.user_profile');

        Route::get('/get_que',[App\Http\Controllers\API\StationController::class, 'get_que'])->name('api.auth.get_que');
        Route::post('/update_que',[App\Http\Controllers\API\StationController::class, 'update_que'])->name('api.auth.update_que');
    });

    //Customer
    Route::middleware(['jwt_customer'])->group(function () {
        Route::post('/customer_logout',[App\Http\Controllers\API\AuthController::class, 'customer_logout'])->name('api.auth.customer_logout');
        Route::get('/customer_profile',[App\Http\Controllers\API\AuthController::class, 'customer_profile'])->name('api.auth.customer_profile');

        //Vehicale Registration
        Route::get('/vechicle_type',[App\Http\Controllers\API\CustomerController::class, 'vechicle_type'])->name('api.vechicle_type');
        Route::post('/customer/addvehicles', [App\Http\Controllers\API\CustomerController::class, 'addvehicles'])->name('api.customer.addvehicles');
        Route::get('/customer/vehicles', [App\Http\Controllers\API\CustomerController::class, 'vehicles'])->name('api.customer.vehicles');
        Route::get('/district_list',[App\Http\Controllers\API\CustomerController::class, 'district_list'])->name('api.district_list');

        //Fuel Request
        Route::post('/customer/que_request/av_fuel', [App\Http\Controllers\Customer\QueRequestController::class, 'av_fuel'])->name('api.customer.que_request.av_fuel');
        Route::post('/customer/que_request/av_fuel_check', [App\Http\Controllers\Customer\QueRequestController::class, 'av_fuel_check'])->name('api.customer.que_request.av_fuel_check');
        Route::post('/customer/que_request/get_station', [App\Http\Controllers\Customer\QueRequestController::class, 'get_station'])->name('api.customer.que_request.get_station');
        Route::post('/customer/que_request/join', [App\Http\Controllers\API\CustomerController::class, 'create_que'])->name('api.customer.que_request.create');
        Route::get('/customer/request_list', [App\Http\Controllers\API\CustomerController::class, 'request_list'])->name('api.customer.request_list');
    });
});


