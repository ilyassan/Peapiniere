<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plants = Plant::all();

        $plants->each(function($plant){
            $plant->images()->saveMany(Image::factory(3)->make()->each(function($image){
                $image->url = "https://via.placeholder.com/150";
            }));
        });

    }
}
