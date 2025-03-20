<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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