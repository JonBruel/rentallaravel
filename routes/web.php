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

Route::get('/customers', 'CustomerController@index');
Route::get('/customer/show/{id}', 'CustomerController@show');
Route::get('/customer/edit/{id}', 'CustomerController@edit');
Route::post('/customer/update/{id}', 'CustomerController@update');


Route::get('/houses', 'HouseController@index');
Route::get('/house/show/{id}', 'HouseController@show');
Route::get('/house/edit/{id}', 'HouseController@edit');
Route::post('/house/update/{id}', 'HouseController@update');