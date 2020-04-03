<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expense;
use Illuminate\Http\Request;

class AccountController extends Controller
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

    public function addCategory(Request $request) {
        $name = $request['name'];

        if(Category::where('user', auth()->user()->id)->where('name', $name)->exists()) {
            return response()->json(['success' => false, 'msg' => 'You already have a Category with that name.']);
        }

        $category = new Category();
        $category->user = auth()->user()->id;
        $category->name = $name;
        $category->save();

        return response()->json(['success' => true, 'id' => $category->id]);
    }

    public function delCategory(Request $request) {
        $id = $request['id'];
        $new_category = $request['new_category'];

        // change all the expense categories to the new one
        foreach(Expense::where('user', auth()->user()->id)->where('category', $id)->get() as $expense) {
            $expense->category = $new_category;
            $expense->save();
        }

        // delete category
        Category::where('user', auth()->user()->id)->where('id', $id)->delete();

        return response()->json(['success' => true]);
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
