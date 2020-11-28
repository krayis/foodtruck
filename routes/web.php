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

Route::group(['middleware' => ['auth'], 'prefix' => 'merchant', 'as' => 'merchant.'], function () {
    Route::resource('schedule', 'Merchant\ScheduleController');

    Route::get('location/search', 'Merchant\LocationController@search')->name('location@search');
    Route::post('location/find', 'Merchant\LocationController@find')->name('location.find');
    Route::resource('location', 'Merchant\LocationController');

    Route::resource('calendar', 'Merchant\CalendarController');
    Route::resource('status', 'Merchant\StatusController');

    Route::post('menu/modifier/category/{category}/modifier/sort', 'Merchant\Menu\Modifier\GroupController@sort')->name('truck.menu.modifier.category.modifier.sort');
    Route::resource('menu/modifier/group', 'Merchant\Menu\Modifier\GroupController', ['as' => 'menu.modifier']);
    Route::resource('menu/modifier', 'Merchant\Menu\ModifierController', ['as' => 'menu']);

    Route::resource('inventory/templates', 'Merchant\Inventory\TemplateController', ['as' => 'inventory', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    Route::resource('inventory/sheets', 'Merchant\Inventory\SheetController', ['as' => 'inventory', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    Route::resource('menu/category', 'Merchant\Menu\CategoryController', ['as' => 'menu']);
    Route::resource('menu/item', 'Merchant\Menu\ItemController', ['as' => 'menu', 'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    Route::resource('menu', 'Merchant\MenuController', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);


    Route::resource('orders', 'Merchant\OrderController');


    Route::resource('settings/advertise', 'Merchant\Settings\AdvertiseController', ['as' => 'settings']);
    Route::resource('settings/throttle', 'Merchant\Settings\ThrottleController', ['as' => 'settings']);
    Route::resource('settings/coupons', 'Merchant\Settings\CouponController', ['as' => 'settings']);
    Route::resource('settings/alerts', 'Merchant\Settings\AlertController', ['as' => 'settings']);
    Route::resource('settings/payments', 'Merchant\Settings\PaymentController', ['as' => 'settings']);
    Route::patch('settings/password', 'Merchant\Settings\GeneralController@updatePassword')->name('settings.password.update');
    Route::resource('settings', 'Merchant\Settings\GeneralController');
});


Route::group(['middleware' => ['api']], function () {
    Route::get('api/item/{item}', 'ItemController@index')->name('api.menu.item');
    Route::get('api/search/trucks', 'SearchController@trucks')->name('api.search@trucks');
    Route::get('api/search', 'SearchController@address')->name('api.search@address');
    Route::get('api/search/suggestions', 'SearchController@suggestions')->name('api.search@suggestions');
});


Route::get('api/truck/{truck}', 'TruckController@index')->name('api.truck');
Route::get('checkout/{id}', 'SearchController@index')->name('search.index');
Route::get('menu/{id}', 'SearchController@index')->name('search.index');
Route::resource('api/checkout', 'CheckoutController');
Route::get('search', 'SearchController@index')->name('search.index');
Route::post('merchant/logout', 'Merchant\LoginController@logout')->name('merchant.logout');
Route::resource('login', 'Merchant\LoginController', ['as' => 'merchant']);
Route::resource('register', 'Merchant\RegisterController', ['as' => 'merchant']);


Route::get('partner', 'PageController@partner');
