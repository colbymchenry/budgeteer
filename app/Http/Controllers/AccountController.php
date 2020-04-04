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

    public function updateMonthlyIncome(Request $request) {
        $amount = $request['amount'];

        $user = auth()->user();
        $user->monthly_income = $amount;
        $user->save();

        return response()->json(['success' => true]);
    }

}
