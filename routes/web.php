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
Route::get('/games', 'GamesController@index');
Route::post('/games/search', 'GamesController@search');
Route::post('/games/add', 'GamesController@add');
Route::post('/games/showOne', 'GamesController@showOne');
Route::post('/games/showList', 'GamesController@showList');
Route::post('/games/update', 'GamesController@update');
Route::post('/games/importCSV', 'GamesController@importCSV');
Route::post('/games/get_platform_list', 'GamesController@get_platform_list');
Route::post('/games/get_genre_list', 'GamesController@get_genre_list');
Route::post('/games/delete', 'GamesController@destroy');
Route::get('/games/getImportTemplate', 'GamesController@getImportTemplate');

Route::post('/filters/add', 'FilterController@add');
Route::post('/filters/delete', 'FilterController@delete');
Route::post('/filters/apply', 'FilterController@apply');
Route::post('/filters/updateName', 'FilterController@updateName');
Route::post('/filters/updateFilter', 'FilterController@updateFilter');
Route::post('/filters/list', 'FilterController@list');