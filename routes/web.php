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

Route::get('/', 'HomeController@showinfo');

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout' );

Route::impersonate();

Route::get('/home', 'HomeController@showinfo');

Route::get('/customer/index', 'CustomerController@index');
Route::get('/customer/show/{id}', 'CustomerController@show');
Route::get('/customer/edit/{id}', 'CustomerController@edit');
Route::get('/customer/destroy/{id}', 'CustomerController@destroy');
Route::post('/customer/update/{id}', 'CustomerController@update');
Route::get('/customer/create', 'CustomerController@create');
Route::post('/customer/store', 'CustomerController@store');

Route::get('/house/index', 'HouseController@index');
Route::get('/house/show/{id}', 'HouseController@show');
Route::get('/house/edit/{id}', 'HouseController@edit');
Route::post('/house/update/{id}', 'HouseController@update');
Route::get('/house/destroy/{id}', 'HouseController@destroy');
Route::get('/house/create', 'HouseController@create');
Route::post('/house/store', 'HouseController@store');

Route::get('/home/showinfo/{infotype}', 'HomeController@showinfo');
Route::get('/home/listtestimonials', 'HomeController@listtestimonials');
Route::get('/home/showmap', 'HomeController@showmap');
Route::get('/home/checkbookings', 'HomeController@checkbookings');
Route::get('/home/listhouses', 'HomeController@listhouses');
