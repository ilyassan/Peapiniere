<?php

namespace App\DAO;

interface OrderDAOInterface
{
    public function getAll($user);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
}