<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;
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

    public function update(int $id, UpdatePlantRequest $request)
    {
        try {
            $plant = Plant::find($id);

            if (!$plant) {
                return response()->json([
                    "status" => false,
                    "message" => "Plant not found",
                ], 404);
            }

            $plant->name = $request->name ?? $plant->name;
            $plant->category_id = $request->category_id ?? $plant->category_id;
            $plant->save();

            if ($request->has("images")) {
                $plant->images()->delete();

                foreach ($request->images as $url) {
                    Image::create([
                        "url" => $url,
                        "plant_id" => $plant->id
                    ]);
                }
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

    public function destroy(int $id)
    {
        try {
            $plant = Plant::find($id);

            if (!$plant) {
                return response()->json([
                    "status" => false,
                    "message" => "Plant not found",
                ], 404);
            }

            $plant->delete();

            return response()->json([
                "status" => true,
                "message" => "Plant deleted",
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}
