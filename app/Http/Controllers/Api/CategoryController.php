<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('shop_id', auth()->user()->shop_id)->get();

        return response([
            'message' => 'Successfully retrieved categories',
            'data' => $categories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::create($validatedData);

        return response([
            'message' => 'Successfully created category',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Category not found'], 404);
        }

        return response([
            'message' => 'Successfully retrieved category',
            'data' => $category,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $category = Category::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Category not found'], 404);
        }

        $category->update($validatedData);

        return response([
            'message' => 'Successfully updated category',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Throwable $th) {
            return response(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response([
            'message' => 'Successfully deleted category',
        ], 200);
    }
}
