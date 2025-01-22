<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //  1. 取得所有產品
    public function index()
    {
        \Log::info('Auth User:', ['user' => auth()->user()]);
        \Log::info('Token Scopes:', ['scopes' => auth()->user()->token()->scopes]);
        $products = Product::all();
        return response()->json($products);
    }

    //  2. 建立新產品
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255',
        ]);

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    //  3. 取得單一產品
    public function show(Product $product)
    {
        return response()->json($product);
    }

    //  4. 更新產品
    public function update(Request $request, Product $product)
    {
        $data = array_filter($request->all(), function ($value) {
            return $value !== '' && $value !== null;
        });

        $request->replace($data);
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|integer|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'sku' => 'sometimes|string|max:255',
        ]);

        $product->update($request->all());
        return response()->json($product);
    }

    //  5. 刪除產品
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}


// GET /api/products - 取得所有產品。
// POST /api/products - 建立新產品。
// GET /api/products/{id} - 取得單一產品。
// PUT /api/products/{id} - 更新產品。
// DELETE /api/products/{id} - 刪除產品。
