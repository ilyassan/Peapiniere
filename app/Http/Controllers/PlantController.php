<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;
use App\Repositories\PlantRepositoryInterface;

class PlantController extends Controller
{
    protected $plantRepository;

    public function __construct(PlantRepositoryInterface $plantRepository)
    {
        $this->plantRepository = $plantRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/plants",
     *     summary="Get a list of plants",
     *     description="Retrieves a list of all plants.",
     *     operationId="getPlants",
     *     tags={"Plants"},
     *     @OA\Response(
     *         response=200,
     *         description="List of plants",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Rose"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="slug", type="string", example="rose"),
     *                 @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://example.com/image.jpg"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $plants = $this->plantRepository->all();
            return response()->json($plants, 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/plants/{slug}",
     *     summary="Get a plant by slug",
     *     description="Retrieves a plant by its slug.",
     *     operationId="getPlantBySlug",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug of the plant to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Rose"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="slug", type="string", example="rose"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://example.com/image.jpg"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Plant not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function show($slug)
    {
        try {
            $plant = $this->plantRepository->findBySlug($slug);
            return response()->json($plant, 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/plants",
     *     summary="Create a new plant",
     *     description="Creates a new plant with the provided details.",
     *     operationId="createPlant",
     *     tags={"Plants"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "images"},
     *             @OA\Property(property="name", type="string", example="Rose"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://example.com/image.jpg"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plant created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Rose"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="slug", type="string", example="rose"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://example.com/image.jpg"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function store(StorePlantRequest $request)
    {
        try {
            $plant = $this->plantRepository->create($request->all());
            return response()->json($plant, 201);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/plants/{slug}",
     *     summary="Update a plant",
     *     description="Updates a plant by its slug.",
     *     operationId="updatePlant",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug of the plant to update",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id"},
     *             @OA\Property(property="name", type="string", example="Rose"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://example.com/image.jpg"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Rose"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="slug", type="string", example="rose"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://example.com/image.jpg"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Plant not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function update($slug, UpdatePlantRequest $request)
    {
        try {
            $plant = $this->plantRepository->update($slug, $request->all());
            return response()->json($plant, 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/plants/{slug}",
     *     summary="Delete a plant",
     *     description="Deletes a plant by its slug.",
     *     operationId="deletePlant",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug of the plant to delete",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Plant deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Plant not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function destroy($slug)
    {
        try {
            $this->plantRepository->delete($slug);
            return response()->json([
                "status" => true,
                "message" => "Plant deleted successfully."
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}