<?php

namespace Tests\Unit;

use App\Http\Controllers\PlantController;
use App\Repositories\PlantRepositoryInterface;
use Mockery;
use Tests\TestCase;

class PlantTest extends TestCase
{
    protected $plantRepository;
    protected $controller;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->plantRepository = Mockery::mock(PlantRepositoryInterface::class);
        $this->controller = new PlantController($this->plantRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_show_returns_plant_when_found_by_slug()
    {
        $plantData = [
            'id' => 1,
            'name' => 'Rose',
            'category_id' => 1,
            'slug' => 'rose',
            'images' => ['https://example.com/rose.jpg']
        ];

        $this->plantRepository
            ->shouldReceive('findBySlug')
            ->with('rose')
            ->once()
            ->andReturn($plantData);

        $response = $this->controller->show('rose');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($plantData, $response->getData(true));
    }

    public function test_show_returns_404_when_plant_not_found()
    {
        $this->plantRepository
            ->shouldReceive('findBySlug')
            ->with('nonexistent-slug')
            ->once()
            ->andReturn(null);

        $response = $this->controller->show('nonexistent-slug');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Plant not found'
        ], $response->getData(true));
    }

    public function test_show_handles_exception()
    {
        $this->plantRepository
            ->shouldReceive('findBySlug')
            ->with('rose')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $this->controller->show('rose');

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Database error'
        ], $response->getData(true));
    }
}