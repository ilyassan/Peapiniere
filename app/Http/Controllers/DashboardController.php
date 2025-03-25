<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     summary="Get dashboard statistics",
     *     description="Retrieves sales statistics, most popular plants, and their distribution by category.",
     *     operationId="getDashboardStatistics",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard statistics retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="totalOrders", type="integer", example=50),
     *             @OA\Property(property="mostPopularPlants", type="array", @OA\Items(
     *                 @OA\Property(property="plant_name", type="string", example="Ficus"),
     *                 @OA\Property(property="orders_count", type="integer", example=25)
     *             )),
     *             @OA\Property(property="categoryDistribution", type="array", @OA\Items(
     *                 @OA\Property(property="category_name", type="string", example="Indoor"),
     *                 @OA\Property(property="plant_count", type="integer", example=100)
     *             ))
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
            $totalOrders = Order::count();

            $mostPopularPlants = DB::table('order_plant')
                ->select('plants.name', DB::raw('count(order_plant.plant_id) as orders_count'))
                ->join('plants', 'plants.id', '=', 'order_plant.plant_id')
                ->groupBy('plants.name')
                ->orderByDesc('orders_count')
                ->limit(5)
                ->get();

            $categoryDistribution = DB::table('plants')
                ->select('categories.name as category_name', DB::raw('count(plants.id) as plant_count'))
                ->join('categories', 'categories.id', '=', 'plants.category_id')
                ->groupBy('categories.name')
                ->get();

            return response()->json(compact('totalOrders', 'mostPopularPlants', 'categoryDistribution'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
