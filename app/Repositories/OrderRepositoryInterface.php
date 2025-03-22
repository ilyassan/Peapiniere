<?php

namespace App\Repositories;

interface OrderRepositoryInterface
{
    public function all($user);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
}