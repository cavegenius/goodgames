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

// TODO: Create a proper Welcome Page
/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', 'Auth\LoginController@showLoginForm');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/games', 'GamesController@index')->middleware('verified');
Route::post('/games/search', 'GamesController@search')->middleware('verified');
Route::post('/games/add', 'GamesController@add')->middleware('verified');
Route::post('/games/showOne', 'GamesController@showOne')->middleware('verified');
Route::post('/games/showList', 'GamesController@showList')->middleware('verified');
Route::post('/games/update', 'GamesController@update')->middleware('verified');
Route::post('/games/importCSV', 'GamesController@importCSV')->middleware('verified');
Route::get('/games/exportCSV', 'GamesController@exportCSV')->middleware('verified');
Route::post('/games/get_platform_list', 'GamesController@get_platform_list')->middleware('verified');
Route::post('/games/get_genre_list', 'GamesController@get_genre_list')->middleware('verified');
Route::post('/games/delete', 'GamesController@destroy')->middleware('verified');
Route::get('/games/getImportTemplate', 'GamesController@getImportTemplate')->middleware('verified');

Route::post('/filters/add', 'FilterController@add')->middleware('verified');
Route::post('/filters/delete', 'FilterController@delete')->middleware('verified');
Route::post('/filters/apply', 'FilterController@apply')->middleware('verified');
Route::post('/filters/updateName', 'FilterController@updateName')->middleware('verified');
Route::post('/filters/updateFilter', 'FilterController@updateFilter')->middleware('verified');
Route::post('/filters/filterList', 'FilterController@list')->middleware('verified');