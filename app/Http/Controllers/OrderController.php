<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get a list of orders",
     *     description="Retrieves a list of orders. If the user is a client, only their orders are returned.",
     *     operationId="getOrders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="client_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *                 @OA\Property(property="plants", type="array", @OA\Items(type="integer", example=1))
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
            if (user()->isClient()) {
                $orders = user()->orders;
            } else {
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

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get an order by ID",
     *     description="Retrieves an order by its ID.",
     *     operationId="getOrderById",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *             @OA\Property(property="plants", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
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

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update an order",
     *     description="Updates an order by its ID. Clients can only cancel their own pending orders.",
     *     operationId="updateOrder",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="cancelled")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="cancelled"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *             @OA\Property(property="plants", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not allowed to update this order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
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

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     description="Creates a new order with the provided details.",
     *     operationId="createOrder",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plants_ids"},
     *             @OA\Property(property="plants_ids", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
     *             @OA\Property(property="plants", type="array", @OA\Items(type="integer", example=1))
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
            DB::rollBack();
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}