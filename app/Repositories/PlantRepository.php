<?php

namespace App\Repositories;

use App\Models\Plant;
use App\Models\Image;

class PlantRepository implements PlantRepositoryInterface
{
    protected $plant;
    protected $image;

    public function __construct(Plant $plant, Image $image)
    {
        $this->plant = $plant;
        $this->image = $image;
    }

    public function all()
    {
        return $this->plant->all();
    }

    public function findBySlug($slug)
    {
        return $this->plant->where('slug', $slug)->firstOrFail();
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

        return $plant;
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

        return $plant;
    }

    public function delete($slug)
    {
        $plant = $this->plant->where('slug', $slug)->firstOrFail();
        $plant->delete();
        return $plant;
    }
}