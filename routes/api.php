<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('konsumen/login', 'KonsumenController@login')->name('konsumen.login');
Route::post('konsumen/register', 'KonsumenController@register')->name('konsumen.register');
Route::get('konsumen/pengerjaan_aktif/{id}', 'PengerjaanController@getPengerjaanAktif')->name('konsumen.pengerjaan_aktif');
Route::get('konsumen/pengerjaan_selesai/{id}', 'PengerjaanController@getPengerjaanSelesai')->name('konsumen.pengerjaan_selesai');
Route::post('konsumen/update/{id}', 'KonsumenController@update_profil')->name('konsumen.update');