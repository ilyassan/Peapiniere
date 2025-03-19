<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesIds = Category::pluck('id')->toArray();
        Plant::factory(100)->make()->each(function($plant) use ($categoriesIds){
            $plant->category_id = Arr::random($categoriesIds);
            $plant->save();
        });

    }
}