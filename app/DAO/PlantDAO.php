<?php

namespace App\DAO;

use App\Models\Plant;
use App\Models\Image;

class PlantDAO implements PlantDAOInterface
{
    protected $plant;
    protected $image;

    public function __construct(Plant $plant, Image $image)
    {
        $this->plant = $plant;
        $this->image = $image;
    }

    public function getAll()
    {
        return $this->plant->with("images", "category")->orderBy("updated_at", "desc")->get();
    }

    public function findBySlug($slug)
    {
        return $this->plant->where('slug', $slug)->with("images", "category")->firstOrFail();
    }

    public function create(array $data)
    {
        $plant = $this->plant->create([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
        ]);

        foreach ($data['images'] as $url) {
            $this->image->create([
                'url' => $url,
                'plant_id' => $plant->id,
            ]);
        }

        return $plant->load("category", "images");
    }

    public function update($slug, array $data)
    {
        $plant = $this->plant->where('slug', $slug)->firstOrFail();

        $plant->update([
            'name' => $data['name'] ?? $plant->name,
            'category_id' => $data['category_id'] ?? $plant->category_id,
        ]);

        if (isset($data['images'])) {
            $plant->images()->delete();

            foreach ($data['images'] as $url) {
                $this->image->create([
                    'url' => $url,
                    'plant_id' => $plant->id,
                ]);
            }
        }

        return $plant->load("category", "images");
    }

    public function delete($slug)
    {
        $plant = $this->plant->where('slug', $slug)->firstOrFail();
        $plant->delete();
        return $plant;
    }
}