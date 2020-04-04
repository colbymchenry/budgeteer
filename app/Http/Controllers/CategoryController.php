<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expense;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $category->limit = $request['limit'];
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

    public function updateLimits(Request $request) {
        $category_id = $request['category_id'];
        $limit = $request['limit'];

        if(!Category::where('user', auth()->user()->id)->where('id', $category_id)->exists()) {
            return response()->json(['success' => false, 'msg' => 'Could not find that category.']);
        }

        $category = Category::where('user', auth()->user()->id)->where('id', $category_id)->get()[0];
        $category->limit = $limit;
        $category->save();

        return response()->json(['success' => true]);
    }

    public function renameCategory(Request $request) {
        $category_id = $request['category_id'];
        $name = $request['name'];

        if(!Category::where('user', auth()->user()->id)->where('id', $category_id)->exists()) {
            return response()->json(['success' => false, 'msg' => 'Could not find that category.']);
        }

        $category = Category::where('user', auth()->user()->id)->where('id', $category_id)->get()[0];
        $category->name = $name;
        $category->save();

        return response()->json(['success' => true]);
    }

}
