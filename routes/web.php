<?php

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
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::view('/account', 'account')->name('account');

    Route::post('/submit_account_changes', 'AccountController@submitChanges')->name('submit_account_changes');

    Route::post('/add_category', 'AccountController@addCategory')->name('add_category');

    Route::post('/del_category', 'AccountController@delCategory')->name('del_category');

    Route::post('/edit_category', 'AccountController@editCategory')->name('edit_category');

    Route::post('/add_expense', 'AccountController@addExpense')->name('add_expense');

    Route::post('/del_expense', 'AccountController@delExpense')->name('del_expense');

    Route::post('/edit_expense', 'AccountController@editExpense')->name('edit_expense');

});


