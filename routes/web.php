<?php

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/*===============================
========== MASTER DATA ==========
=================================
*/
Route::group(['prefix' => 'customer'], function()
{
    Route::get('/', 'CustomerController@index')->name('customer.index');
    Route::post('/store', 'CustomerController@store')->name('customer.store');
    Route::post('/update/{customer}', 'CustomerController@update')->name('customer.update');
    Route::post('/delete/{customer}', 'CustomerController@destroy')->name('customer.delete');
}); 

Route::group(['prefix' => 'employee'], function()
{
    Route::get('/', 'EmployeeController@index')->name('employee.index');
    Route::post('/store', 'EmployeeController@store')->name('employee.store');
    Route::post('/update/{employee}', 'EmployeeController@update')->name('employee.update');
    Route::post('/delete/{employee}', 'EmployeeController@destroy')->name('employee.delete');
}); 

Route::group(['prefix' => 'seller'], function()
{
    Route::get('/', 'SellerController@index')->name('seller.index');
    Route::post('/store', 'SellerController@store')->name('seller.store');
    Route::post('/update/{seller}', 'SellerController@update')->name('seller.update');
    Route::post('/delete/{seller}', 'SellerController@destroy')->name('seller.delete');
}); 

Route::group(['prefix' => 'pegawai'], function()
{
    Route::get('/', 'PegawaiController@index')->name('pegawai.index');
    Route::post('/store', 'PegawaiController@store')->name('pegawai.store');
    Route::get('/edit/{pegawai}', 'PegawaiController@edit')->name('pegawai.edit');
    Route::post('/update/{pegawai}', 'PegawaiController@update')->name('pegawai.update');
    Route::post('/delete/{pegawai}', 'PegawaiController@destroy')->name('pegawai.delete');
}); 

Route::group(['prefix' => 'pengerjaan'], function()
{
    Route::get('/', 'PengerjaanController@index')->name('pengerjaan.index');
    Route::post('/store', 'PengerjaanController@store')->name('pengerjaan.store');
    Route::post('/update/{id}', 'PengerjaanController@update')->name('pengerjaan.update');
    Route::post('/delete/{pengerjaan}', 'PengerjaanController@destroy')->name('pengerjaan.delete');
    Route::get('/print/{pengerjaan}', 'PengerjaanController@print')->name('pengerjaan.print');
}); 

/*===============================
========== TRANSACTION ==========
=================================
*/
Route::group(['prefix' => 'transaction'], function()
{
    Route::group(['prefix' => 'regular'], function()
    {
        Route::get('/', 'RegularTxController@index')->name('tx.regular.index');
        Route::get('/new', 'RegularTxController@create')->name('tx.regular.new');
        Route::post('/store', 'RegularTxController@store')->name('tx.regular.store');
        Route::get('/draft/{tx}', 'RegularTxController@draft')->name('tx.regular.draft');
        Route::post('/update/{tx}', 'RegularTxController@update')->name('tx.regular.update');
        Route::post('/delete/{tx}', 'RegularTxController@destroy')->name('tx.regular.delete');
        Route::get('/invoice/{tx}', 'RegularTxController@invoice')->name('tx.regular.invoice');
    }); 

    Route::group(['prefix' => 'compliment'], function()
    {
        Route::get('/', 'ComplimentTxController@index')->name('tx.compliment.index');
        Route::get('/new', 'ComplimentTxController@create')->name('tx.compliment.new');
        Route::post('/store', 'ComplimentTxController@store')->name('tx.compliment.store');
        Route::get('/draft/{tx}', 'ComplimentTxController@draft')->name('tx.compliment.draft');
        Route::post('/update/{tx}', 'ComplimentTxController@update')->name('tx.compliment.update');
        Route::post('/delete/{tx}', 'ComplimentTxController@destroy')->name('tx.compliment.delete');
        Route::get('/invoice/{tx}', 'ComplimentTxController@invoice')->name('tx.compliment.invoice');
    }); 
}); 

/*===============================
=========== INVENTORY ===========
=================================
*/
Route::group(['prefix' => 'unit'], function()
{
    Route::get('/', 'UnitController@index')->name('unit.index');
    Route::post('/store', 'UnitController@store')->name('unit.store');
    Route::post('/update/{unit}', 'UnitController@update')->name('unit.update');
    Route::post('/delete/{unit}', 'UnitController@destroy')->name('unit.delete');
});

Route::group(['prefix' => 'goods'], function()
{
    Route::get('/', 'GoodsController@index')->name('goods.index');
    Route::post('/store', 'GoodsController@store')->name('goods.store');
    Route::post('/update/{goods}', 'GoodsController@update')->name('goods.update');
    Route::post('/delete/{goods}', 'GoodsController@destroy')->name('goods.delete');
});

Route::group(['prefix' => 'receiving'], function()
{
    Route::get('/', 'ReceivingController@index')->name('receiving.index');
    Route::get('/new', 'ReceivingController@create')->name('receiving.new');
    Route::post('/store', 'ReceivingController@store')->name('receiving.store');
    Route::get('/edit/{receiving}', 'ReceivingController@edit')->name('receiving.edit');
    Route::post('/update/{receiving}', 'ReceivingController@update')->name('receiving.update');
    Route::post('/delete/{receiving}', 'ReceivingController@destroy')->name('receiving.delete');
    Route::get('/invoice/{receiving}', 'ReceivingController@invoice')->name('receiving.invoice');
});

Route::group(['prefix' => 'breakage'], function()
{
    Route::get('/', 'BreakageController@index')->name('breakage.index');
    Route::post('/store', 'BreakageController@store')->name('breakage.store');
    Route::post('/update/{breakage}', 'BreakageController@update')->name('breakage.update');
    Route::post('/delete/{breakage}', 'BreakageController@destroy')->name('breakage.delete');
});

Route::group(['prefix' => 'supplier'], function()
{
    Route::get('/', 'SupplierController@index')->name('supplier.index');
    Route::post('/store', 'SupplierController@store')->name('supplier.store');
    Route::post('/update/{supplier}', 'SupplierController@update')->name('supplier.update');
    Route::post('/delete/{supplier}', 'SupplierController@destroy')->name('supplier.delete');
});
