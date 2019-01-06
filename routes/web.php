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
Route::get('/spotify', 'SpotifyConnectorController@index');
Route::get('/spotify/connect', 'SpotifyConnectorController@connect');
Route::get('/spotify/accept-auth', 'SpotifyConnectorController@acceptAuth');
Route::get('/player', 'PlayerController@index');
