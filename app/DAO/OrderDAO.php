<?php

namespace App\DAO;

use App\Models\Order;
use App\Models\Plant;
use Illuminate\Support\Facades\DB;

class OrderDAO implements OrderDAOInterface
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getAll($user)
    {
        if ($user->isClient()) {
            return $user->orders;
        }
        return $this->order->all();
    }

    public function find($id)
    {
        return $this->order->find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        $order = $this->order->create([
            "client_id" => $data['user_id'],
        ]);

        foreach ($data["plants"] as $plant) {
            $id = Plant::findBySlug($plant['slug'])->id;
            $order->plants()->attach($id, ['quantity' => $plant['quantity']]);
        }

        DB::commit();

        return $order->fresh();
    }

    public function update($id, array $data)
    {
        $order = $this->order->find($id);

        if (!$order) {
            return null;
        }

        if ($data['user']->isClient() && (($order->client_id != $data['user']->id) || ($data['status'] != "cancelled" && $order->status != "pending"))) {
            return false; // forbidden action
        }

        $order->status = $data['status'];
        $order->save();

        return $order;
    }
}