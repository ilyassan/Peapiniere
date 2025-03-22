<?php

namespace App\Repositories;

use App\DAO\PlantDAOInterface;

class PlantRepository implements PlantRepositoryInterface
{
    protected $plantDAO;

    public function __construct(PlantDAOInterface $plantDAO)
    {
        $this->plantDAO = $plantDAO;
    }

    public function all()
    {
        return $this->plantDAO->getAll();
    }

    public function findBySlug($slug)
    {
        return $this->plantDAO->findBySlug($slug);
    }

    public function create(array $data)
    {
        return $this->plantDAO->create($data);
    }

    public function update($slug, array $data)
    {
        return $this->plantDAO->update($slug, $data);
    }

    public function delete($slug)
    {
        return $this->plantDAO->delete($slug);
    }
}