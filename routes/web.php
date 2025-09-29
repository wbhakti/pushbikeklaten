<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', 'App\Http\Controllers\HomeController@index');
Route::get('/login', 'App\Http\Controllers\UserController@login')->name('login');
Route::get('/logout', 'App\Http\Controllers\UserController@logout')->name('logout');
Route::get('/register', 'App\Http\Controllers\UserController@register');

Route::get('/dashboard', 'App\Http\Controllers\HomeController@dashboard')->name('dashboard');
Route::get('/dashboard/slider', 'App\Http\Controllers\MasterController@slider');
Route::get('/dashboard/event', 'App\Http\Controllers\MasterController@event');
Route::get('/dashboard/listregistration', 'App\Http\Controllers\UserController@listregistration');
Route::get('/dashboard/kategori', 'App\Http\Controllers\MasterController@kategori');
Route::get('/getreportevent', 'App\Http\Controllers\UserController@reportevent');
Route::get('/cekstatus', 'App\Http\Controllers\UserController@cekstatus');
Route::get('/statustransaksi/{id_transaksi}', 'App\Http\Controllers\UserController@statustransaksi')->name('statusTransaksi');

Route::post('/postlogin', 'App\Http\Controllers\UserController@postlogin');
Route::post('/postslider', 'App\Http\Controllers\MasterController@postslider');
Route::post('/postevent', 'App\Http\Controllers\MasterController@postevent');
Route::post('/postkategori', 'App\Http\Controllers\MasterController@postkategori');
Route::post('/postregister', 'App\Http\Controllers\UserController@postregister');
Route::post('/postbuktitransfer', 'App\Http\Controllers\UserController@postbuktitransfer');
Route::post('/postapproveuser', 'App\Http\Controllers\UserController@postapproveuser');
