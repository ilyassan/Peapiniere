<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plant;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     summary="Get dashboard statistics",
     *     description="Retrieves statistics for the dashboard, including total plants, orders, ordered plants, and clients.",
     *     operationId="getDashboardStatistics",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard statistics retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="totalPlants", type="integer", example=100),
     *             @OA\Property(property="totalOrders", type="integer", example=50),
     *             @OA\Property(property="totalOrderedPlants", type="integer", example=200),
     *             @OA\Property(property="totalClients", type="integer", example=30)
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
            $totalPlants = Plant::count();
            $totalOrders = Order::count();
            $totalOrderedPlants = Order::withCount('plants')->get()->sum('plants_count');
            $totalClients = User::clients()->count();

            return response()->json(compact('totalPlants', 'totalOrders', 'totalOrderedPlants', 'totalClients'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}