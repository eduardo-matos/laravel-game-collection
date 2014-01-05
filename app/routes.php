<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::model('game', 'Game');

Route::group(['before' => 'auth'], function ()
{
	Route::get('/', 'GamesController@index');
	Route::get('/create', 'GamesController@create')->before('auth');
	Route::get('/edit/{game}', 'GamesController@edit');
	Route::get('/delete/{game}', 'GamesController@delete');

	Route::post('/create', 'GamesController@handleCreate');
	Route::post('/edit', 'GamesController@handleEdit');
	Route::post('/delete/{game}', 'GamesController@handleDelete');
});

Route::get('/login', 'LoginController@index');
Route::post('/login', 'LoginController@handleLogin');
Route::get('/logout', 'LoginController@logout');
Route::get('/signup', 'LoginController@signup');
Route::post('/signup', 'LoginController@handleSignup');
