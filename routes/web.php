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

/**
 * Non-Google Auth
 */
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

/**
 * Google Auth
 */
Route::prefix('auth/google')->group(function () {
    Route::get('/', 'Auth\GoogleController@auth')->name('auth.google');
    Route::get('callback', 'Auth\GoogleController@callback')->name('auth.google.callback');
});

/**
 * Downloads
 */
Route::get('download/report/{project}', 'DownloadController@report')->name('download.report');

/**
 * Network Graph PoC
 */
Route::get('graph/{project}', 'GraphController@index')->name('graph');