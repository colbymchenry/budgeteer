<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getBankAccount() {
        return BankAccount::where('user', $this->id)->get()[0];
    }

    public function getFunMoneyExpensesForMonth($month) {
        return Expense::where('user', $this->id)->where('category', '-1')->whereMonth('created_at', intval($month))->get();
    }

    public function getFunMoneySpentForMonth($month) {
        return Expense::where('user', $this->id)->where('category', '-1')->whereMonth('created_at', intval($month))->sum('amount');
    }

    public function getFunMoneyCategory() {
        $total_budget = Category::where('user', $this->id)->sum('limit');
        $fun_money_category = new Category();
        $fun_money_category->id = -1;
        $fun_money_category->limit = $this->monthly_income - $total_budget;
        $fun_money_category->name = "Fun Money";
        $fun_money_category->user = $this->id;
        return $fun_money_category;
    }

    public function getFixedCategories() {
        return Category::where('user', $this->id)->where('recurring', true)->get();
    }

    public function getFixedCategorySum() {
        return Category::where('user', $this->id)->where('recurring', true)->sum('limit');
    }

}
