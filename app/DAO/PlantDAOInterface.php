<?php

namespace App\DAO;

interface PlantDAOInterface
{
    public function getAll();
    public function findBySlug($slug);
    public function create(array $data);
    public function update($slug, array $data);
    public function delete($slug);
}