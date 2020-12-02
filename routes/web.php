<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
})->middleware(['auth.shopify'])->name('home');

Route::middleware('auth.shopify')->group(function () {
    //Route::get('/', 'HomeController@index')->name('home');
    
    Route::get('/home', 'HomeController@index')->name('home_menu');
    
    /****** Order ******/
    Route::get('/order', 'ShopifyOrderController@index')->name('order_index');
    Route::get('/order/name', 'ShopifyOrderController@searchByOrderName')->name('order_name');
    Route::get('/order/name/{order}', 'ShopifyOrderController@getByOrderName')->name('search_order_name');
    Route::get('/order/export', 'ShopifyOrderController@exportTablesToExcel')->name('order_table_export');
});
        
Auth::routes();

