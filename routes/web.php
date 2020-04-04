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

    Route::post('/update_monthly_income', 'AccountController@updateMonthlyIncome')->name('update_monthly_income');

    Route::post('/add_category', 'CategoryController@addCategory')->name('add_category');

    Route::post('/del_category', 'CategoryController@delCategory')->name('del_category');

    Route::post('/rename_category', 'CategoryController@renameCategory')->name('rename_category');

    Route::post('/update_limits', 'CategoryController@updateLimits')->name('update_limits');

    Route::post('/add_expense', 'ExpenseController@addExpense')->name('add_expense');

    Route::post('/del_expense', 'ExpenseController@delExpense')->name('del_expense');

    Route::post('/edit_expense', 'ExpenseController@editExpense')->name('edit_expense');

});


