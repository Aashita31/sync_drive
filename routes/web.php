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

Route::get('glogin',array('as'=>'glogin','uses'=>'UserController@googleLogin')) ;
Route::get('upload-file',array('as'=>'upload-file','uses'=>'UserController@uploadFileUsingAccessToken')) ;
Route::get('/delete', 'UserController@delete'); // delete file
Route::get('/add'.'UserController@addFolder');
 