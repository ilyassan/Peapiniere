<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Models\Image;
use App\Models\Plant;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function index()
    {
        try {
            $plants = Plant::all();

            return response()->json($plants, 200);

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
            $plant = Plant::find($id);

            if (!$plant) {
                return response()->json([
                    "status" => false,
                    "message" => "Plant not found",
                ], 404);
            }

            return response()->json($plant, 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function store(StorePlantRequest $request)
    {
        try {
            $plant = Plant::create([
                "name" => $request->name, 
                "category_id" => $request->category_id, 
            ]);

            foreach ($request->images as $url) {
                Image::create([
                    "url" => $url,
                    "plant_id" => $plant->id
                ]);
            }

            return response()->json($plant, 201);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}
