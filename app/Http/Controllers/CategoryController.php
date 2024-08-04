<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        return response()->json([
            "categories" => $categories
        ]);
    }

    public function create(Request $request)
    {

        $request->validate([
            "name" => ["required", "string", 'min:4']
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'status' => "success",
            "category" => $category
        ]);
    }
}