<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'UserController@store');
Route::post('login', 'UserController@login');
Route::post('logout', 'UserController@logout');



Route::get('posts', 'PostController@index');
Route::get('posts/{title}', 'PostController@show');
Route::post('posts', 'PostController@store');
Route::post('posts/{title}/comments', 'PostController@postComment');
Route::patch('posts/{title}', 'PostController@patch');
Route::patch('posts/{title}/comments/{id}', 'PostController@patchComment');
Route::delete('posts/{title}', 'PostController@delete');
Route::delete('posts/{title}/comments/{id}', 'PostController@deleteComment');
