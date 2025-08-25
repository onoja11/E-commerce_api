<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    public function search(Request $request)
{
    $query = $request->input('query');

    if (!$query) {
        return response()->json(['message' => 'Query parameter is required'], 400);
    }

    $products = Product::where('name', 'like', "%{$query}%")
        ->orWhere('description', 'like', "%{$query}%")
        ->with('category')
        ->get();

    $categories = Category::where('name', 'like', "%{$query}%")->get();

    if ($categories->isNotEmpty()) {
        $categoryProducts = Product::whereIn('category_id', $categories->pluck('id'))
            ->with('category')
            ->get();

        $products = $products->merge($categoryProducts);
    }

    $products = $products->unique('id')->values();

    return response()->json([
        'products' => $products,
    ]);
}

}
