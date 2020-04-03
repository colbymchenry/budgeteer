<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Expense;

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

        $expenses = array();

        for($i = 12; $i > 0; $i--) {
            $expenses_for_month = Expense::where('id', auth()->user()->id)->whereMonth('created_at', $i)->get();
            $expenses[$i] = $expenses_for_month;
        }

        return view('home')->with('month', $month)->with('year', $year)->with('day', $day)->with('expenses', $expenses);
    }
}
