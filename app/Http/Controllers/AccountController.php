<?php

namespace App\Http\Controllers;

use App\BankAccount;
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

    public function updateBankAccount(Request $request) {
        $balance = $request['balance'];
        $next_paycheck = $request['next_paycheck'];

        $bankaccount = BankAccount::where('user', auth()->user()->id)->get()[0];
        $bankaccount->balance = $balance;
        $bankaccount->next_paycheck = $next_paycheck;
        $bankaccount->save();

        $now = time();
        $your_date = strtotime(auth()->user()->getBankAccount()->next_paycheck);
        $datediff = $your_date - $now;

        $days_next_paycheck = round($datediff / (60 * 60 * 24));
        $daily_budget = Category::where('user', auth()->user()->id)->sum('limit') / 30;
        $monthly_budget = $days_next_paycheck * round($daily_budget);
        $fun_money = auth()->user()->getBankAccount()->balance - ($days_next_paycheck * round($daily_budget));

        return response()->json(['success' => true,
        'days_next_paycheck' => $days_next_paycheck, 'daily_budget' => round($daily_budget),
        'monthly_budget' => $monthly_budget, 'fun_money' => $fun_money]);
    }

    public function updateMonthlyIncomeBankAcct(Request $request) {
        $balance = $request['balance'];
        $next_paycheck = $request['next_paycheck'];

        $bankaccount = BankAccount::where('user', auth()->user()->id)->get()[0];
        $bankaccount->balance = $balance;
        $bankaccount->next_paycheck = $next_paycheck;
        $bankaccount->save();

        $now = time();
        $your_date = strtotime(auth()->user()->getBankAccount()->next_paycheck);
        $datediff = $your_date - $now;

        $days_next_paycheck = round($datediff / (60 * 60 * 24));

        $user = auth()->user();
        $user->monthly_income = $bankaccount->balance / ($days_next_paycheck / 30.42);
        $user->save();

        return response()->json(['success' => true, 'msg' => 'Monthly Income Now: $' . $user->monthly_income]);
    }

}
