<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use \App\Expense;
use DateTime;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $now = new \DateTime('now');
        $month = $now->format('m');
        $year = $now->format('Y');
        $day = $now->format('d');

        // add up all expenses
        $expenses = array();
        for($i = 12; $i > 0; $i--) {
            $expenses_for_month = Expense::where('user', auth()->user()->id)->whereMonth('created_at', $i)->get();
            $expenses[$i] = $expenses_for_month;
        }

        // setup month selection html
        $month_selections_html = array();
        for($i = 1; $i < 13; $i++) {
            $dateObj   = DateTime::createFromFormat('!m', $i);
            $monthName = $dateObj->format('F');
            if(intval($month) == $i) {
                $month_selections_html[$i] = '<option value="' . $i . '" selected="selected">' . $monthName . '</option>';
            } else {
                $month_selections_html[$i] = '<option value="' . $i . '">' . $monthName . '</option>';
            }
        }

        // don't add fun money expenses or fixed expenses to actual expenses
        $actual_expenses = 0;
        foreach(\App\Expense::where('user', auth()->user()->id)->where('category', '!=', '-1')->whereMonth('created_at', intval($month))->get() as $expense) {
            $category = Category::where('id', $expense->category)->first();
            if(! $category->recurring) $actual_expenses += $expense->amount;
        }
        $actual_expenses += auth()->user()->getFixedCategorySum();
        $left_for_budget = \App\Category::where('user', auth()->user()->id)->sum('limit') - $actual_expenses;

        return view('home')->with('month', intval($month))->with('year', $year)->with('day', $day)
        ->with('expenses', $expenses)->with('month_selections_html', $month_selections_html)
        ->with('fun_money', auth()->user()->getFunMoneyCategory())->with('actual_expenses', $actual_expenses)
        ->with('left_for_budget', $left_for_budget)->with('fun_money_spent', auth()->user()->getFunMoneySpentForMonth());
    }
}
