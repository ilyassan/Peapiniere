<?php

namespace App\DTO;

class OrderDTO
{
    public int $id;
    public $client;
    public string $status;
    public string $created_at;
    public string $updated_at;
    public array $plants;

    public function __construct(
        int $id,
        $client,
        string $status,
        string $created_at,
        string $updated_at,
        array $plants
    ) {
        $this->id = $id;
        $this->client = $client;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->plants = $plants;
    }
}
