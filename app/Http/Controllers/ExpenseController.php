<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expense;
use Illuminate\Http\Request;

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

        return response()->json(['success' => true, 'expense' => $expense]);
    }

    public function viewExpenses(Request $request) {
        $category_id = \request('category');
        $month = \request('month');

        $category = Category::where('user', auth()->user()->id)->where('id', $category_id)->get()[0];
        $expenses = Expense::where('user', auth()->user()->id)->where('category', $category_id)->whereMonth('created_at', $month)->get();

        return view('expense_list')->with('expenses', $expenses)->with('category', $category);
    }

}
