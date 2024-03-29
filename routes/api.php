<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me',  'App\Http\Controllers\AuthController@me');
    Route::post('register', 'App\Http\Controllers\AuthController@register');
});
Route::middleware('jwt.verify')->group(function () {
    Route::post('create', 'App\Http\Controllers\BooksController@create');
    Route::get('getbooks', 'App\Http\Controllers\BooksController@getbooks');
    Route::get('getbook/{id}', 'App\Http\Controllers\BooksController@getbook');
    Route::get('statistics', 'App\Http\Controllers\BooksController@statistics');
});
