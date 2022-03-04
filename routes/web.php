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

Route::get('/','lineController@reMessage')->name('reMessage');
Route::post('/','lineController@reMessage')->name('reMessage');

Route::get('/mytle','lineController@mytles')->name('mytles');
Route::post('/mytle','lineController@mytles')->name('mytles');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
