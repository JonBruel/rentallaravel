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
Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');

Route::impersonate();

Route::get('/home', 'HomeController@showinfo');

//All MyAccountController
Route::get('/myaccount/registration', 'MyAccountController@registration');
Route::post('/myaccount/updateregistration', 'MyAccountController@updateregistration');
Route::get('/myaccount/listbookings', 'MyAccountController@listbookings');
Route::get('/myaccount/listaccountposts', 'MyAccountController@listaccountposts');
Route::get('/myaccount/listmails', 'MyAccountController@listmails');
Route::get('/myaccount/edittime', 'MyAccountController@edittime');
Route::post('/myaccount/updatetime', 'MyAccountController@updatetime');
Route::get('/myaccount/gdpr', 'MyAccountController@gdpr');
Route::post('/myaccount/destroycustomer/{id}', 'MyAccountController@destroycustomer');


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
Route::get('/customer/merge/{id}', 'CustomerController@merge');
Route::post('/customer/merge', 'CustomerController@merge');
Route::get('/customer/checkaccount/{id}', 'CustomerController@checkaccount');


Route::get('/house/show/{id}', 'HouseController@show');
Route::get('/house/edit/{id}', 'HouseController@edit');
Route::post('/house/update/{id}', 'HouseController@update');
Route::delete('/house/destroy/{id}', 'HouseController@destroy');
//Route::get('/house/create', 'HouseController@create');
//Route::post('/house/store', 'HouseController@store');
//From rental:
Route::get('/house/createperiods', 'HouseController@createperiods');
Route::post('/house/createperiods', 'HouseController@createperiods');

Route::post('/house/insertcreatedperiods', 'HouseController@insertcreatedperiods');
Route::get('/house/listperiods', 'HouseController@listperiods');
Route::get('/house/listhouses', 'HouseController@listhouses');
Route::get('/house/listperiods', 'HouseController@listperiods');
Route::get('/house/edithousehtml/{id}', 'HouseController@edithousehtml');
Route::post('/house/updatehousehtml/{id}', 'HouseController@updatehousehtml');
Route::get('/house/browse/{id}', 'HouseController@browse');
Route::post('/house/deletefiles/{id}', 'HouseController@deletefiles');
Route::get('/house/editperiod/{id}', 'HouseController@editperiod');
Route::post('/house/updateperiod/{id}', 'HouseController@updateperiod');
Route::post('/house/updateperiods', 'HouseController@updateperiods');

Route::get('/contract/listcontractoverview', 'ContractController@listcontractoverview');
Route::get('/contract/listcontractoverviewforowners', 'ContractController@listcontractoverviewforowners');
Route::get('/contract/show/{id}', 'ContractController@show');
Route::get('/contract/contractedit/{id}/{periodid}', 'ContractController@contractedit');
Route::get('/contract/commitcontract/{id}', 'ContractController@commitcontract');
Route::post('/contract/commitcontract', 'ContractController@commitcontract');
Route::post('/contract/commitcontract/{id}', 'ContractController@commitcontract');
Route::post('/contract/contractupdate/{id}', 'ContractController@contractupdate');
Route::get('/contract/listaccountposts/{contractid}', 'ContractController@listaccountposts');
Route::get('/contract/listmails/{customerid}', 'ContractController@listmails');
Route::get('/contract/annualcontractoverview', 'ContractController@annualcontractoverview');
Route::post('/contract/registerpayment/{contractid}', 'ContractController@registerpayment');
Route::get('/accountpost/edit/{id}', 'ContractController@accountpostedit');
Route::delete('/accountpost/destroy/{id}', 'ContractController@accountpostdestroy');
Route::post('/accountpost/update/{id}', 'ContractController@accountpostupdate');


Route::get('/home/showinfo/{infotype}', 'HomeController@showinfo');
Route::get('/home/listtestimonials', 'HomeController@listtestimonials');
Route::post('/home/createtestimonial', 'HomeController@createtestimonial');
Route::get('/home/showmap', 'HomeController@showmap');
Route::get('/home/checkbookings', 'HomeController@checkbookings');
Route::get('/home/checkbookingslist', 'HomeController@checkbookingslist');
Route::get('/home/search', 'HomeController@search');
Route::post('/home/search', 'HomeController@search');
Route::get('/home/listhouses', 'HomeController@listhouses');
Route::get('/home/tokenlogin', 'HomeController@tokenlogin');
Route::delete('/testimonial/destroy/{id}', 'HomeController@destroytestimonial');
Route::get('/testimonial/edit/{id}', 'HomeController@edittestimonial');
Route::post('/testimonial/edit/{id}', 'HomeController@updatetestimonial');



Route::get('/setup/listbatchtasks', 'SetupController@listbatchtasks');
Route::post('/setup/listbatchtasks', 'SetupController@listbatchtasks');
Route::get('/setup/editbatchtask/{id}', 'SetupController@editbatchtask');
Route::post('/setup/updatebatchtask/{id}', 'SetupController@updatebatchtask');

Route::get('/setup/liststandardemails', 'SetupController@liststandardemails');
Route::get('/setup/editstandardemail/{id}', 'SetupController@editstandardemail');
Route::get('/setup/updatestandardemail/{id}', 'SetupController@updatestandardemail');
Route::post('/setup/updatestandardemail/{id}', 'SetupController@updatestandardemail');

Route::get('/setup/makebatch1', 'SetupController@makebatch1');
Route::get('/setup/copybatch/{houseid}/{overwrite}/{batchexists}', 'SetupController@copybatch');
Route::get('/setup/showphpinfo', 'SetupController@showphpinfo');

Route::get('/setup/editcaptions', 'SetupController@editcaptions');
Route::post('/setup/updatecaptions/{id}', 'SetupController@updatecaptions');

Route::get('/setup/editconfig', 'SetupController@editconfig');
Route::post('/setup/updateconfig', 'SetupController@updateconfig');

Route::get('setup/gdprdelete', 'SetupController@gdprdelete');


Route::get('/setup/listqueue', 'SetupController@listqueue');
Route::get('/batchlog/edit/{id}', 'SetupController@editbatchlog');
Route::delete('/batchlog/destroy/{id}', 'SetupController@destroybatchlog');
Route::post('/batchlog/update/{id}', 'SetupController@updatebatchlog');

Route::get('/wizards/workflow', 'SetupController@workflow');

Route::get('/setup/firstsetup', 'SetupController@firstsetup');

Route::get('/setup/listerrorlogs', 'SetupController@listerrorlogs');

Route::get('/setup/edittranslations', 'SetupController@edittranslations');
Route::post('/setup/edittranslations', 'SetupController@edittranslations');
Route::get('/setup/updatephpdoc', 'SetupController@updatephpdoc');
Route::get('/setup/showdocumentation', 'SetupController@showdocumentation');

Route::get('/setup/listbounties', 'SetupController@listbounties');

//From rental:
Route::get('/contract/annualcontractoverview', 'ContractController@annualcontractoverview');

//AJAX
Route::get('/ajax/ajaxlisthouses/x1/{x1}/y1/{y1}/x2/{x2}/y2/{y2}', 'AjaxController@listhouses');
Route::get('/ajax/getweeks/{houseid}/{culture}/{offset}/{periodid}/{contractid}', 'AjaxController@getweeks');
Route::get('/ajax/getmonths/{houseid}', 'AjaxController@getmonths');
Route::get('/ajax/getimportstatus', 'AjaxController@getImportStatus');
Route::get('/ajax/getgdprdeletestatus', 'AjaxController@getGdprDeleteStatus');

//For the migration from the old version of rental
Route::get('/import/importfromrental', 'ImportController@importfromrental');
Route::post('/import/importfromrental', 'ImportController@importfromrental');
