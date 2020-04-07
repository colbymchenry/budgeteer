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

        $net = auth()->user()->monthly_income - Expense::where('user', auth()->user()->id)->whereMonth('created_at', intval($month))->sum('amount');
        $total_budget = Category::where('user', auth()->user()->id)->sum('limit');
        $budgeted_fun_money = auth()->user()->monthly_income - $total_budget;

        if($net < $budgeted_fun_money) {
            $budgeted_fun_money = $net;
        }

        $actual_expenses = \App\Expense::where('user', auth()->user()->id)->whereMonth('created_at', intval($month))->sum('amount');
        $left_for_budget = \App\Category::where('user', auth()->user()->id)->sum('limit') - \App\Expense::where('user', auth()->user()->id)->whereMonth('created_at', intval($month))->sum('amount');

        return view('home')->with('month', intval($month))->with('year', $year)->with('day', $day)
        ->with('expenses', $expenses)->with('month_selections_html', $month_selections_html)
        ->with('fun_money', $budgeted_fun_money)->with('actual_expenses', $actual_expenses)->with('left_for_budget', $left_for_budget);
    }
}
