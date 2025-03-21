<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function index()
    {
        try {
            $categories = Category::all();

            return response()->json($categories, 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "status" => false,
                    "message" => "Category not found",
                ], 404);
            }

            return response()->json($category, 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $plant = Category::create([
                "name" => $request->name, 
            ]);

            return response()->json($plant, 201);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function update(int $id, UpdateCategoryRequest $request)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "status" => false,
                    "message" => "Category not found",
                ], 404);
            }

            $category->name = $request->name;
            $category->save();


            return response()->json($category, 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "status" => false,
                    "message" => "Category not found",
                ], 404);
            }

            $category->delete();

            return response()->json([
                "status" => true,
                "message" => "Category deleted successfully."
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}
