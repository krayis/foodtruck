<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function() {
    Route::resource('schedule', 'Truck\ScheduleController', ['as' => 'truck']);

    /**
     * Location routes
     */
    Route::get('location/search', 'Truck\LocationController@search')->name('truck.location@search');
    Route::post('location/find', 'Truck\LocationController@find')->name('truck.location.find');
    Route::resource('location', 'Truck\LocationController', ['as' => 'truck']);

    Route::resource('calendar', 'Truck\CalendarController');
    Route::resource('status', 'Truck\StatusController', ['as' => 'truck']);

    Route::post('menu/modifier/category/{category}/modifier/sort', 'Truck\Menu\Modifier\GroupController@sort')->name('truck.menu.modifier.category.modifier.sort');
    Route::resource('menu/modifier/group', 'Truck\Menu\Modifier\GroupController', ['as' => 'truck.menu.modifier']);
    Route::resource('menu/modifier', 'Truck\Menu\ModifierController', ['as' => 'truck.menu'] );

    Route::resource('inventory/templates', 'Truck\Inventory\TemplateController', ['as' => 'admin.inventory', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']] );
    Route::resource('inventory/sheets', 'Truck\Inventory\SheetController', ['as' => 'admin.inventory', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']] );
    Route::resource('menu', 'Truck\MenuController', ['as' => 'truck', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']] );
    Route::resource('menu/item', 'Truck\Menu\ItemController', ['as' => 'truck.menu', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']] );
    Route::resource('menu/category', 'Truck\Menu\CategoryController', ['as' => 'truck.menu']);


    Route::resource('orders', 'Truck\OrderController', ['as' => 'truck']);


    Route::resource('settings/advertise', 'Truck\Settings\AdvertiseController',  ['as' => 'truck.settings']);
    Route::resource('settings/throttle', 'Truck\Settings\ThrottleController',  ['as' => 'truck.settings']);
    Route::resource('settings/coupons', 'Truck\Settings\CouponController',  ['as' => 'truck.settings']);
    Route::resource('settings/alerts', 'Truck\Settings\AlertController',  ['as' => 'truck.settings']);
    Route::resource('settings/payments', 'Truck\Settings\PaymentController',  ['as' => 'truck.settings']);
    Route::patch('settings/password', 'Truck\Settings\GeneralController@updatePassword')->name('truck.settings.password.update');
    Route::resource('settings', 'Truck\Settings\GeneralController',  ['as' => 'truck']);
});


Route::group(['middleware' => ['api']], function() {
    Route::get('api/item/{item}', 'ItemController@index')->name('api.menu.item');
    Route::get('api/search/trucks', 'SearchController@trucks')->name('api.search@trucks');
    Route::get('api/search', 'SearchController@address')->name('api.search@address');
    Route::get('api/search/suggestions', 'SearchController@suggestions')->name('api.search@suggestions');
});

Route::get('oauth/square', 'SquareController@oauth');
Route::get('oauth/square/cb', 'SquareController@callback');
Route::get('pay', 'SquareController@paymentPage');
Route::post('process-payment', 'SquareController@processPayment');
Route::get('api/truck/{truck}', 'TruckController@index')->name('api.truck');
Route::get('checkout/{id}', 'SearchController@index')->name('search.index');
Route::get('menu/{id}', 'SearchController@index')->name('search.index');
Route::resource('api/checkout', 'CheckoutController');
Route::get('search', 'SearchController@index')->name('search.index');
Route::post('truck/logout', 'Truck\LoginController@logout')->name('truck.logout');
Route::resource('login', 'Truck\LoginController', ['as' => 'truck']);
Route::resource('register', 'Truck\RegisterController', ['as' => 'truck']);


Route::get('partner', 'PageController@partner');
