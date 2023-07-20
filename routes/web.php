<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Customers ============================================================================================================================================
Route::get('/', [App\Http\Controllers\Customer\AuthController::class, 'login_form'])->name('fuelin.customer.login.view');
Route::post('/customer/login', [App\Http\Controllers\Customer\AuthController::class, 'login'])->name('fuelin.customer.login');
Route::get('/customer/register', [App\Http\Controllers\Customer\AuthController::class, 'register_form'])->name('fuelin.customer.register.view');
Route::post('/customer/register', [App\Http\Controllers\Customer\AuthController::class, 'register'])->name('fuelin.customer.register');

Route::middleware(['customer'])->group(function () {
    Route::post('/customer/logout', [App\Http\Controllers\Customer\AuthController::class, 'logout'])->name('fuelin.customer.logout');

    Route::get('/customer', [App\Http\Controllers\Customer\IndexController::class, 'index'])->name('fuelin.customer');
    Route::get('/customer/get_vehicles', [App\Http\Controllers\Customer\IndexController::class, 'get_vehicles'])->name('fuelin.customer.get_vehicles');
    Route::get('/customer/addvehicles', [App\Http\Controllers\Customer\IndexController::class, 'add_form'])->name('fuelin.customer.add.form');
    Route::post('/customer/addvehicles', [App\Http\Controllers\Customer\IndexController::class, 'create'])->name('fuelin.customer.create');

    Route::get('/customer/que_request', [App\Http\Controllers\Customer\QueRequestController::class, 'index'])->name('fuelin.customer.que_request');
    Route::get('/customer/que_request_get', [App\Http\Controllers\Customer\QueRequestController::class, 'que_request_get'])->name('fuelin.customer.que_request.get');
    Route::get('/customer/que_request/join', [App\Http\Controllers\Customer\QueRequestController::class, 'add_form'])->name('fuelin.customer.que_request.add_form');
    Route::post('/customer/que_request/av_fuel', [App\Http\Controllers\Customer\QueRequestController::class, 'av_fuel'])->name('fuelin.customer.que_request.av_fuel');
    Route::post('/customer/que_request/av_fuel_check', [App\Http\Controllers\Customer\QueRequestController::class, 'av_fuel_check'])->name('fuelin.customer.que_request.av_fuel_check');
    Route::post('/customer/que_request/get_station', [App\Http\Controllers\Customer\QueRequestController::class, 'get_station'])->name('fuelin.customer.que_request.get_station');
    Route::post('/customer/que_request/join', [App\Http\Controllers\Customer\QueRequestController::class, 'create'])->name('fuelin.customer.que_request.create');
    Route::post('/customer/que_request/delete', [App\Http\Controllers\Customer\QueRequestController::class, 'delete'])->name('fuelin.customer.que_request.delete');
});



//Admin ============================================================================================================================================

Route::group(['prefix' => 'fuelin'], function () {
    Auth::routes(['register' => false]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/fuelin', [App\Http\Controllers\Fulein\IndexController::class, 'index'])->name('fuelin.dashboard');
    Route::get('/fuelin/admin/monthly_fuel', [App\Http\Controllers\Fulein\IndexController::class, 'admin_monthly_fuel'])->name('fuelin.dashboard.admin.monthly_fuel');
    Route::get('/fuelin/admin/monthly_cust_fuel', [App\Http\Controllers\Fulein\IndexController::class, 'admin_monthly_cust_fuel'])->name('fuelin.dashboard.admin.monthly_cust_fuel');

    Route::get('/fuelin/manger/monthly_fuel', [App\Http\Controllers\Fulein\IndexController::class, 'manager_monthly_fuel'])->name('fuelin.dashboard.manager.monthly_fuel');

    //Fuel Station
    Route::get('/fuelin/fuel_station', [App\Http\Controllers\Fulein\FuelStationController::class, 'index'])->name('fuelin.fuel_station');
    Route::get('/fuelin/fuel_station_get', [App\Http\Controllers\Fulein\FuelStationController::class, 'fuel_station_get'])->name('fuelin.fuel_station.get');
    Route::get('/fuelin/fuel_station/create', [App\Http\Controllers\Fulein\FuelStationController::class, 'add_form'])->name('fuelin.fuel_station.add_form');
    Route::post('/fuelin/fuel_station/create', [App\Http\Controllers\Fulein\FuelStationController::class, 'create'])->name('fuelin.fuel_station.create');
    Route::get('/fuelin/fuel_station/update/{id}', [App\Http\Controllers\Fulein\FuelStationController::class, 'update_form'])->name('fuelin.fuel_station.update_form');
    Route::post('/fuelin/fuel_station/update', [App\Http\Controllers\Fulein\FuelStationController::class, 'update'])->name('fuelin.fuel_station.update');
    Route::post('/fuelin/fuel_station/delete', [App\Http\Controllers\Fulein\FuelStationController::class, 'delete'])->name('fuelin.fuel_station.delete');

    //Rate Card
    Route::get('/fuelin/rate_card', [App\Http\Controllers\Fulein\RateCardController::class, 'index'])->name('fuelin.rate_card');
    Route::post('/fuelin/rate_card/update', [App\Http\Controllers\Fulein\RateCardController::class, 'update'])->name('fuelin.rate_card.update');
    Route::get('/fuelin/rate_card/get_vechile_type', [App\Http\Controllers\Fulein\RateCardController::class, 'get_vechile_type'])->name('fuelin.rate_card.get_vechile_type');

    //Fuel Station Users
    Route::get('/fuelin/station_user', [App\Http\Controllers\Fulein\StationUserController::class, 'index'])->name('fuelin.station_user');
    Route::get('/fuelin/station_user_get', [App\Http\Controllers\Fulein\StationUserController::class, 'station_user_get'])->name('fuelin.station_user.get');
    Route::get('/fuelin/station_user/create', [App\Http\Controllers\Fulein\StationUserController::class, 'add_form'])->name('fuelin.station_user.add_form');

    Route::post('/fuelin/station_user/create', [App\Http\Controllers\Fulein\StationUserController::class, 'create'])->name('fuelin.station_user.create');
    Route::get('/fuelin/station_user/update/{id}', [App\Http\Controllers\Fulein\StationUserController::class, 'update_form'])->name('fuelin.station_user.update_form');
    Route::post('/fuelin/station_user/update', [App\Http\Controllers\Fulein\StationUserController::class, 'update'])->name('fuelin.station_user.update');
    Route::post('/fuelin/station_user/delete', [App\Http\Controllers\Fulein\StationUserController::class, 'delete'])->name('fuelin.station_user.delete');

    //Customers
    Route::get('/fuelin/customers', [App\Http\Controllers\Fulein\CustomerController::class, 'index'])->name('fuelin.customers');
    Route::get('/fuelin/customers_get', [App\Http\Controllers\Fulein\CustomerController::class, 'customers_get'])->name('fuelin.customers.get');
    Route::get('/fuelin/customers/update/{id}', [App\Http\Controllers\Fulein\CustomerController::class, 'update_form'])->name('fuelin.customers.update_form');
    Route::post('/fuelin/customers/update', [App\Http\Controllers\Fulein\CustomerController::class, 'update'])->name('fuelin.customers.update');
    Route::post('/fuelin/customers/delete', [App\Http\Controllers\Fulein\CustomerController::class, 'delete'])->name('fuelin.customers.delete');

    //Customer Vehicle
    Route::get('/fuelin/customers/vehicles/{id}', [App\Http\Controllers\Fulein\CustomerVehicleController::class, 'index'])->name('fuelin.customers.vehicles');
    Route::get('/fuelin/customers/vehicles_get/{id}', [App\Http\Controllers\Fulein\CustomerVehicleController::class, 'vehicles_get'])->name('fuelin.customers.vehicles.get');
    Route::get('/fuelin/customers/vehicles/update/{id}', [App\Http\Controllers\Fulein\CustomerVehicleController::class, 'update_form'])->name('fuelin.customers.update_form');
    Route::post('/fuelin/customers/vehicles/update', [App\Http\Controllers\Fulein\CustomerVehicleController::class, 'update'])->name('fuelin.customers.update');
    Route::post('/fuelin/customers/vehicles/delete', [App\Http\Controllers\Fulein\CustomerVehicleController::class, 'delete'])->name('fuelin.customers.delete');

    //Station Fuel Request
    Route::get('/fuelin/request_fuel', [App\Http\Controllers\Fulein\StationFuelRequestController::class, 'index'])->name('fuelin.request_fuel');
    Route::get('/fuelin/request_fuel_get', [App\Http\Controllers\Fulein\StationFuelRequestController::class, 'request_fuel_get'])->name('fuelin.request_fuel.get');
    Route::post('/fuelin/request_fuel_get/create', [App\Http\Controllers\Fulein\StationFuelRequestController::class, 'create'])->name('fuelin.request_fuel.create');
    Route::get('/fuelin/request_fuel/update/{id}', [App\Http\Controllers\Fulein\StationFuelRequestController::class, 'update_form'])->name('fuelin.request_fuel.update.form');
    Route::post('/fuelin/request_fuel/update', [App\Http\Controllers\Fulein\StationFuelRequestController::class, 'update'])->name('fuelin.request_fuel.update');

    //Station Fuel Request
    Route::get('/fuelin/fuel_station_request', [App\Http\Controllers\Fulein\FuelStationRequestController::class, 'index'])->name('fuelin.fuel_station_request');
    Route::get('/fuelin/fuel_station_request_get', [App\Http\Controllers\Fulein\FuelStationRequestController::class, 'request_fuel_get'])->name('fuelin.fuel_station_request.get');
    Route::get('/fuelin/fuel_station_request/update/{id}', [App\Http\Controllers\Fulein\FuelStationRequestController::class, 'update_form'])->name('fuelin.fuel_station_request.update.form');
    Route::post('/fuelin/fuel_station_request/update', [App\Http\Controllers\Fulein\FuelStationRequestController::class, 'update'])->name('fuelin.fuel_station_request.update');

    //Delivery Schedule
    Route::get('/fuelin/schedules', [App\Http\Controllers\Fulein\DeliveryScheduleController::class, 'index'])->name('fuelin.schedules');
    Route::get('/fuelin/schedules_get', [App\Http\Controllers\Fulein\DeliveryScheduleController::class, 'schedules_get'])->name('fuelin.schedules.get');
    Route::post('/fuelin/schedules/update', [App\Http\Controllers\Fulein\DeliveryScheduleController::class, 'update'])->name('fuelin.schedules.update');

    //Customer Fuel Request
    Route::get('/fuelin/request_fuel_cus', [App\Http\Controllers\Fulein\CustomerFuelRequestController::class, 'index'])->name('fuelin.request_fuel_cus');
    Route::get('/fuelin/request_fuel_cus_get', [App\Http\Controllers\Fulein\CustomerFuelRequestController::class, 'request_fuel_cus_get'])->name('fuelin.request_fuel_cus.get');
    Route::post('/fuelin/request_fuel_cus/update', [App\Http\Controllers\Fulein\CustomerFuelRequestController::class, 'update'])->name('fuelin.request_fuel_cus.update');

    //Reports
    Route::get('/fuelin/report', [App\Http\Controllers\Fulein\ReportController::class, 'index'])->name('fuelin.report');
    Route::post('/fuelin/get_admin_report', [App\Http\Controllers\Fulein\ReportController::class, 'getAdminReport'])->name('fuelin.getAdminReport');
    Route::post('/fuelin/report_validation', [App\Http\Controllers\Fulein\ReportController::class, 'report_validation'])->name('fuelin.report_validation');
});

//Common
Route::post('/fuelin/station_user/get_station', [App\Http\Controllers\Fulein\StationUserController::class, 'get_station'])->name('fuelin.station_user.get_station');

// $monday = strtotime("last monday");
// $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
// $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
// $from_date = date('Y-m-d', $monday);
// $to_date = date('Y-m-d', $sunday);
