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

        $expense = new Expense();
        $expense->user = auth()->user()->id;
        $expense->category = $category;
        $expense->amount = $amount;
        $expense->save();

        return response()->json(['success' => true, 'expense' => $expense]);
    }

}
