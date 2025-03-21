<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        try {
            if (user()->isClient()) {
                $orders = user()->orders;
            }else{
                $orders = Order::all();
            }

            return response()->json($orders, 200);

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
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "status" => false,
                    "message" => "Order not found",
                ], 404);
            }

            return response()->json($order, 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function update(int $id, UpdateOrderRequest $request)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "status" => false,
                    "message" => "Order not found",
                ], 404);
            }

            if (user()->isClient() && (($order->client_id != user()->id) || ($request->status != "cancelled" && $order->status != "pending"))) {
                return response()->json([
                    "status" => false,
                    "message" => "You are not allowed to update this order",
                ], 403);
            }

            $order->status = $request->status;
            $order->save();

            return response()->json($order, 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                "client_id" => user()->id,
            ]);

            $order->plants()->attach($request->plants_ids);

            DB::commit();

            return response()->json($order, 201);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

}
