<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // use the fake here

        $clientsIds = User::clients()->pluck("id")->toArray();

        $orders = Order::factory(ceil(count($clientsIds) / 2))->make()->each(function ($order) use ($clientsIds) {
            $order->client_id = Arr::random($clientsIds);
            $order->save();
        });


        // attach each order to a random plants
        $plants = Plant::pluck('id')->toArray();

        $orders->each(function ($order) use ($plants) {
            $order->plants()->attach(Arr::random($plants, rand(1, 3)));
        });
    }
}
