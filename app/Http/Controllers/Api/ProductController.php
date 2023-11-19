<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ]);

        $products = Product::select('id', 'name', 'description', 'price', 'stock', 'created_by')->where('shop_id', auth()->user()->shop_id);

        // check using DB Postgres or MySQL
        if (config('database.default') === 'pgsql') {
            $products->where('name', 'ilike', '%' . $validatedData['keyword'] . '%');
        } else {
            $products->where('name', 'like', '%' . $validatedData['keyword'] . '%');
        }

        if (isset($validatedData['category_id'])) {
            $products->whereHas('categories', function ($query) use ($validatedData) {
                $query->where('category_id', $validatedData['category_id']);
            });
        }

        if (isset($validatedData['page'])) {
            $products = $products->skip(($validatedData['page'] - 1) * $validatedData['per_page'])->take($validatedData['per_page'])->get();
        } else {
            $products = $products->get();
        }

        $products->load('createdBy:id,username');
        $products->load('categories:id,name');

        return response([
            'message' => 'Successfully retrieved products',
            'data' => $products,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:products',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $product = Product::create($validatedData);

        return response([
            'message' => 'Successfully created product',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Product not found'], 404);
        }

        $product->load('createdBy:id,username');
        $product->load('categories:id,name');

        return response([
            'message' => 'Successfully retrieved product',
            'data' => $product,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        try {
            $product = Product::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Product not found'], 404);
        }

        $product->update($validatedData);

        return response([
            'message' => 'Successfully updated product',
            'data' => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response(['message' => 'Successfully deleted product'], 200);
    }

    /**
     * Add category to product.
     */
    public function addCategory(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            $product = Product::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Product not found'], 404);
        }

        // check if category already exists
        if ($product->categories()->where('category_id', $validatedData['category_id'])->exists()) {
            return response(['message' => 'Category already exists'], 400);
        }

        $product->categories()->attach($validatedData['category_id'], [
            'created_by' => auth()->user()->id,
        ]);

        return response([
            'message' => 'Successfully added category to product',
            'data' => $product,
        ], 200);
    }

    /**
     * Remove category from product.
     */
    public function removeCategory(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            $product = Product::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Product not found'], 404);
        }

        $product->categories()->detach($validatedData['category_id']);

        return response([
            'message' => 'Successfully removed category from product',
            'data' => $product,
        ], 200);
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ]);

        $products = Product::select('id', 'name', 'description', 'price', 'stock', 'created_by')->where('shop_id', auth()->user()->shop_id)
            ->where('name', 'ilike', '%' . $validatedData['keyword'] . '%');

        if (isset($validatedData['category_id'])) {
            $products->whereHas('categories', function ($query) use ($validatedData) {
                $query->where('category_id', $validatedData['category_id']);
            });
        }

        if (isset($validatedData['page'])) {
            $products = $products->skip(($validatedData['page'] - 1) * $validatedData['per_page'])->take($validatedData['per_page'])->get();
        } else {
            $products = $products->get();
        }

        $products->load('createdBy:id,username');
        $products->load('categories:id,name');

        return response([
            'message' => 'Successfully retrieved products',
            'data' => $products,
        ], 200);
    }
}
