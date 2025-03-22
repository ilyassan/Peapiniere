<?php

namespace App\Repositories;

interface PlantRepositoryInterface
{
    public function all();
    public function findBySlug($slug);
    public function create(array $data);
    public function update($slug, array $data);
    public function delete($slug);
}