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
        
        // Validate the query
        if (!$query) {
            return response()->json(['message' => 'Query parameter is required'], 400);
        }

        // Perform the search in products and categories
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with('category')
            ->get();

        $categories = Category::where('name', 'like', "%{$query}%")
            ->with('products')
            ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
