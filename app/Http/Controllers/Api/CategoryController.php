<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // add category
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            // business_id auto-filled by BelongsToBusiness trait
        ]);

        return response()->json([
            'message' => 'Category added successfully',
            'data' => $category,
        ], 201);
    }

    // get categories for business
    public function getCategories(Request $request)
    {
        $categories = Category::where('business_id', $request->user()->business_id)->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    // update category
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        // Use query() to respect global scope
        $category = Category::query()->findOrFail($id);

        // Verify category belongs to user's business
        if ($category->business_id !== $request->user()->business_id) {
            return response()->json([
                'message' => 'Unauthorized access to this category',
            ], 403);
        }

        $category->name = $request->name;
        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }
}
