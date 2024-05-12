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

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/report', 'ReportController@index')->name('report');

Route::prefix('/iot')->group(function () {
    Route::get('getToken', 'Thingsboard\\IotController@getToken');
    Route::get('authToken', 'Thingsboard\\IotController@authToken');
    Route::get('removeToken', 'Thingsboard\\IotController@removeToken');
    Route::get('getDevices', 'Thingsboard\\IotController@getDevices');
    Route::get('getAttrib', 'Thingsboard\\IotController@getAttrib');
    Route::get('getTelemetry', 'Thingsboard\\IotController@getTelemetry');
});



Route::get('invoice', function(){
    return view('invoice');
});


Route::get('/admin/home',array('as'=>'admin.home', function () {
    return "admin";
}));



Route::get('{path}','HomeController@index')->where( 'path', '([0-9A-Za-z\-]+)?' );
