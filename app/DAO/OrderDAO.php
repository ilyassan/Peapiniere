<?php

namespace App\DAO;

use App\Models\Order;
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

        $order->plants()->attach($data['plants_ids']);

        DB::commit();

        return $order;
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