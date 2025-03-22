<?php

namespace App\Repositories;

use App\DAO\OrderDAOInterface;

class OrderRepository implements OrderRepositoryInterface
{
    protected $orderDAO;

    public function __construct(OrderDAOInterface $orderDAO)
    {
        $this->orderDAO = $orderDAO;
    }

    public function all($user)
    {
        return $this->orderDAO->getAll($user);
    }

    public function find($id)
    {
        return $this->orderDAO->find($id);
    }

    public function create(array $data)
    {
        return $this->orderDAO->create($data);
    }

    public function update($id, array $data)
    {
        return $this->orderDAO->update($id, $data);
    }
}