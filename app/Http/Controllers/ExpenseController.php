<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
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
        return view('home');
    }


    public function addExpense(Request $request) {
        $category = $request['category'];
        $amount = $request['amount'];
        $memo = $request['memo'];

        $expense = new Expense();
        $expense->user = auth()->user()->id;
        $expense->category = $category;
        $expense->amount = $amount;
        if($memo != null || $memo != "") $expense->memo = $memo;
        $expense->save();

        $now = new \DateTime('now');
        $month = $now->format('m');
        $year = $now->format('Y');
        $day = $now->format('d');

        if($category == -1) {
            $total_budget = Category::where('user', auth()->user()->id)->sum('limit');
            $percentage = ((Expense::where('user', auth()->user()->id)->where('category', $category)->whereMonth('created_at', intval($month))->sum('amount')) / (auth()->user()->monthly_income - $total_budget))*100;
        } else {
            $percentage = ((Expense::where('user', auth()->user()->id)->where('category', $category)->whereMonth('created_at', intval($month))->sum('amount')) / (Category::where('user', auth()->user()->id)->where('id', $category)->get()[0]->limit))*100;
        }


        return response()->json(['success' => true, 'expense' => $expense, 'percentage' => $percentage]);
    }

    public function deleteExpense(Request $request) {
        $id = $request['id'];

        $expense = Expense::where('id', $id)->get()[0];
        $expense->delete();

        return response()->json(['success' => true]);
    }

    public function viewExpenses(Request $request) {
        $category_id = \request('category');
        $month = \request('month');

        if($category_id == -1) {
            $category = auth()->user()->getFunMoneyCategory();
            $expenses = Expense::where('user', auth()->user()->id)->where('category', $category_id)->whereMonth('created_at', $month)->orderBy('created_at', 'DESC')->get();
        } else {
            $category = Category::where('user', auth()->user()->id)->where('id', $category_id)->get()[0];
            $expenses = Expense::where('user', auth()->user()->id)->where('category', $category_id)->whereMonth('created_at', $month)->orderBy('created_at', 'DESC')->get();
        }

        return view('expense_list')->with('expenses', $expenses)->with('category', $category);
    }

    public function getExpensesForMonth(Request $request) {
        $month = $request['month'];

        $categories = array();
        foreach(Category::where('user', auth()->user()->id)->get() as $category) {
            $categories[$category->id] = [
                'amount' => $category->getTotalForMonth($month),
                'name' => $category->name,
                'percentage' => round(($category->getTotalForMonth($month) / $category->limit) * 100),
            ];
        }

        $net = auth()->user()->monthly_income - Expense::where('user', auth()->user()->id)->whereMonth('created_at', intval($month))->sum('amount');
        $total_budget = Category::where('user', auth()->user()->id)->sum('limit');
        $budgeted_fun_money = auth()->user()->monthly_income - $total_budget;

        if($net < $budgeted_fun_money) {
            $budgeted_fun_money = $net;
        }

        $actual_expenses = \App\Expense::where('user', auth()->user()->id)->whereMonth('created_at', intval($month))->sum('amount');
        $left_for_budget = \App\Category::where('user', auth()->user()->id)->sum('limit') - \App\Expense::where('user', auth()->user()->id)->whereMonth('created_at', intval($month))->sum('amount');

        return response()->json(['success' => true, 'categories' => $categories, 'actual_expenses' => $actual_expenses, 'left_for_budget' => ($left_for_budget < 0 ? 0 : $left_for_budget), 'fun_money' => $budgeted_fun_money]);
    }

}
