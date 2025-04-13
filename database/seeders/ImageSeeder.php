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
                $image->url = "https://t4.ftcdn.net/jpg/11/25/96/23/360_F_1125962371_D8BU9ZpTBMTihboBGctD9Y6ChtWUNXy1.jpg";
            }));
        });

    }
}
