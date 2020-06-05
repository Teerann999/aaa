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
Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::prefix('categorie')->group(function () {
        Route::get('', 'CategorieController@index');
        Route::post('', 'CategorieController@store');
        Route::patch('{id}', 'CategorieController@update');
        Route::delete('{id}', 'CategorieController@delete');
        Route::get('datatables', 'CategorieController@getDatatables');
        Route::get('form/{id}', 'CategorieController@renderForm');
    });

    Route::prefix('document')->group(function () {
        Route::get('', 'DocumentController@index');
        Route::get('main', 'DocumentController@main');
        Route::post('', 'DocumentController@store');
        Route::patch('{id}', 'DocumentController@update');
        Route::delete('{id}', 'DocumentController@delete');
        Route::get('datatables', 'DocumentController@getDatatables');
        Route::get('form/{id}', 'DocumentController@renderForm');
        Route::get('view/{id}', 'DocumentController@view');
        Route::post('uploadfile', 'DocumentController@uploadfile');
    });

    Route::prefix('user')->group(function () {
        Route::get('', 'UserController@index');
        Route::post('', 'UserController@store');
        Route::patch('{id}', 'UserController@update');
        Route::delete('{id}', 'UserController@delete');
        Route::get('datatables', 'UserController@getDatatables');
        Route::get('form/{id}', 'UserController@renderForm');
        Route::get('username_check', 'UserController@usernameCheck');
    });
});
