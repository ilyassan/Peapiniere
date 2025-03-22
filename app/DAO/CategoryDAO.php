<?php

namespace App\DAO;

use App\Models\Category;

class CategoryDAO implements CategoryDAOInterface
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getAll()
    {
        return $this->category->all();
    }

    public function find($id)
    {
        return $this->category->find($id);
    }

    public function create(array $data)
    {
        return $this->category->create([
            "name" => $data['name'],
        ]);
    }

    public function update($id, array $data)
    {
        $category = $this->category->find($id);

        if (!$category) {
            return null;
        }

        $category->name = $data['name'];
        $category->save();

        return $category;
    }

    public function delete($id)
    {
        $category = $this->category->find($id);

        if (!$category) {
            return false;
        }

        $category->delete();
        return true;
    }
}