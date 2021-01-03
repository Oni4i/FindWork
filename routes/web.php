<?php

use App\User;
use Illuminate\Support\Facades\Route;

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
    return view('index');
})->name('home');

Route::group(['middleware' => 'auth'], function() {
    Route::group(['as' => 'profile', 'prefix' => 'profile', 'namespace' => 'Profile'], function() {
        Route::get('/', 'ProfileController@index');
        Route::group(['as' => '.vacancy', 'prefix' => 'vacancy'], function() {
            Route::get('', 'Vacancy\VacancyController@index')->name('.index');
            Route::post('', 'Vacancy\VacancyController@store')->name('.store');
            Route::delete('', 'Vacancy\VacancyController@destroy')->name('.destroy');
        });
    });
    Route::group(['as' => 'find', 'prefix' => 'find', 'namespace' => 'Find'], function() {
        Route::get('/', 'FindController@index');
        Route::post('/search', 'FindController@search')->middleware('find')->name('.search');
    });
});
Route::middleware('guest')->group(function() {
    //
});

Auth::routes();

