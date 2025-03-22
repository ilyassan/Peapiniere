<?php

namespace App\Repositories;

use App\DAO\CategoryDAOInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $categoryDAO;

    public function __construct(CategoryDAOInterface $categoryDAO)
    {
        $this->categoryDAO = $categoryDAO;
    }

    public function all()
    {
        return $this->categoryDAO->getAll();
    }

    public function find($id)
    {
        return $this->categoryDAO->find($id);
    }

    public function create(array $data)
    {
        return $this->categoryDAO->create($data);
    }

    public function update($id, array $data)
    {
        return $this->categoryDAO->update($id, $data);
    }

    public function delete($id)
    {
        return $this->categoryDAO->delete($id);
    }
}