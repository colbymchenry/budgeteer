<?php

use App\Category;
use Illuminate\Support\Facades\Auth;
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

    Route::get('/bank_account', function() {
        $now = time(); // or your date as well
        $your_date = strtotime(auth()->user()->getBankAccount()->next_paycheck);
        $datediff = $your_date - $now;

        $daily_budget = Category::where('user', auth()->user()->id)->sum('limit') / 30;

        return view('bank_account')->with('days_next_paycheck', round($datediff / (60 * 60 * 24)))
        ->with('daily_budget', $daily_budget);
    })->name('bank_account');

    Route::post('/update_bank_account', 'AccountController@updateBankAccount')->name('update_bank_account');

    Route::post('/update_monthly_income', 'AccountController@updateMonthlyIncome')->name('update_monthly_income');

    Route::post('/add_category', 'CategoryController@addCategory')->name('add_category');

    Route::post('/del_category', 'CategoryController@delCategory')->name('del_category');

    Route::post('/rename_category', 'CategoryController@renameCategory')->name('rename_category');

    Route::post('/update_limits', 'CategoryController@updateLimits')->name('update_limits');

    Route::post('/add_expense', 'ExpenseController@addExpense')->name('add_expense');

    Route::post('/del_expense', 'ExpenseController@delExpense')->name('del_expense');

    Route::post('/edit_expense', 'ExpenseController@editExpense')->name('edit_expense');

    Route::get('/view_expenses', 'ExpenseController@viewExpenses')->name('view_expenses');

    Route::get('/get_expenses_for_month', 'ExpenseController@getExpensesForMonth')->name('get_expenses_for_month');

});


