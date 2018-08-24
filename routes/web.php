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
//Route::get('/customer/destroy/{id}', 'CustomerController@destroy');
//Route::post('/customer/destroy/{id}', 'CustomerController@destroy');
Route::delete('/customer/destroy/{id}', 'CustomerController@destroy');
Route::post('/customer/update/{id}', 'CustomerController@update');
Route::get('/customer/create', 'CustomerController@create');
Route::post('/customer/store', 'CustomerController@store');
//From rental:
Route::get('/customer/statistics', 'CustomerController@statistics');

Route::get('/house/index', 'HouseController@index');
Route::get('/house/show/{id}', 'HouseController@show');
Route::get('/house/edit/{id}', 'HouseController@edit');
Route::post('/house/update/{id}', 'HouseController@update');
Route::get('/house/destroy/{id}', 'HouseController@destroy');
Route::get('/house/create', 'HouseController@create');
Route::post('/house/store', 'HouseController@store');
//From rental:
Route::get('/house/createperiods', 'HouseController@createperiods');
Route::get('/house/listperiods', 'HouseController@listperiods');
Route::get('/house/listhouses', 'HouseController@listhouses');
Route::get('/house/listperiods', 'HouseController@listperiods');

Route::get('/contract/choseweeks', 'ContractController@chooseweeks');
Route::get('/contract/chooseweeks', 'ContractController@chooseweeks');
Route::post('/contract/preparecontract', 'ContractController@preparecontract');
Route::get('/contract/preparecontract', 'ContractController@chooseweeks');
Route::post('/contract/commitcontract', 'ContractController@commitcontract');


Route::get('/contract/annualcontractoverview', 'ContractController@annualcontractoverview');


Route::get('/home/showinfo/{infotype}', 'HomeController@showinfo');
Route::get('/home/listtestimonials', 'HomeController@listtestimonials');
Route::get('/home/showmap', 'HomeController@showmap');
Route::get('/home/checkbookings', 'HomeController@checkbookings');
Route::get('/home/listhouses', 'HomeController@listhouses');

Route::get('/ajax/ajaxlisthouses/x1/{x1}/y1/{y1}/x2/{x2}/y2/{y2}', 'AjaxController@listhouses');

//From rental:
Route::get('/contract/annualcontractoverview', 'ContractController@annualcontractoverview');