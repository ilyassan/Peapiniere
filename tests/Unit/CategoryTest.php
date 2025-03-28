<?php

namespace Tests\Unit;

use App\Http\Controllers\CategoryController;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepositoryInterface;
use Mockery;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    protected $categoryRepository;
    protected $controller;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->controller = new CategoryController($this->categoryRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // Index Tests
    public function test_index_returns_all_categories()
    {
        $categories = [
            ['id' => 1, 'name' => 'Flowers'],
            ['id' => 2, 'name' => 'Plants']
        ];

        $this->categoryRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($categories);

        $response = $this->controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($categories, $response->getData(true));
    }

    public function test_index_handles_exception()
    {
        $this->categoryRepository
            ->shouldReceive('all')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $this->controller->index();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Database error'
        ], $response->getData(true));
    }

    // Show Tests
    public function test_show_returns_category_when_found()
    {
        $category = ['id' => 1, 'name' => 'Flowers'];

        $this->categoryRepository
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($category);

        $response = $this->controller->show(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($category, $response->getData(true));
    }

    public function test_show_returns_404_when_not_found()
    {
        $this->categoryRepository
            ->shouldReceive('find')
            ->with(999)
            ->once()
            ->andReturn(null);

        $response = $this->controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Category not found'
        ], $response->getData(true));
    }

    public function test_show_handles_exception()
    {
        $this->categoryRepository
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $this->controller->show(1);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Database error'
        ], $response->getData(true));
    }

    // Store Tests
    public function test_store_creates_new_category()
    {
        $requestData = ['name' => 'New Category'];
        $createdCategory = ['id' => 3, 'name' => 'New Category'];

        $request = Mockery::mock(StoreCategoryRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);

        $this->categoryRepository
            ->shouldReceive('create')
            ->with($requestData)
            ->once()
            ->andReturn($createdCategory);

        $response = $this->controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($createdCategory, $response->getData(true));
    }

    public function test_store_handles_exception()
    {
        $request = Mockery::mock(StoreCategoryRequest::class);
        $request->shouldReceive('all')->once()->andThrow(new \Exception('Database error'));

        $response = $this->controller->store($request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Database error'
        ], $response->getData(true));
    }

    // Update Tests
    public function test_update_modifies_existing_category()
    {
        $requestData = ['name' => 'Updated Category'];
        $updatedCategory = ['id' => 1, 'name' => 'Updated Category'];

        $request = Mockery::mock(UpdateCategoryRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);

        $this->categoryRepository
            ->shouldReceive('update')
            ->with(1, $requestData)
            ->once()
            ->andReturn($updatedCategory);

        $response = $this->controller->update(1, $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($updatedCategory, $response->getData(true));
    }

    public function test_update_returns_404_when_not_found()
    {
        $requestData = ['name' => 'Updated Category'];
        
        $request = Mockery::mock(UpdateCategoryRequest::class);
        $request->shouldReceive('all')->once()->andReturn($requestData);

        $this->categoryRepository
            ->shouldReceive('update')
            ->with(999, $requestData)
            ->once()
            ->andReturn(null);

        $response = $this->controller->update(999, $request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Category not found'
        ], $response->getData(true));
    }

    public function test_update_handles_exception()
    {
        $request = Mockery::mock(UpdateCategoryRequest::class);
        $request->shouldReceive('all')->once()->andThrow(new \Exception('Database error'));

        $response = $this->controller->update(1, $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Database error'
        ], $response->getData(true));
    }

    // Destroy Tests
    public function test_destroy_deletes_category()
    {
        $this->categoryRepository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $response = $this->controller->destroy(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'status' => true,
            'message' => 'Category deleted successfully.'
        ], $response->getData(true));
    }

    public function test_destroy_returns_404_when_not_found()
    {
        $this->categoryRepository
            ->shouldReceive('delete')
            ->with(999)
            ->once()
            ->andReturn(false);

        $response = $this->controller->destroy(999);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Category not found'
        ], $response->getData(true));
    }

    public function test_destroy_handles_exception()
    {
        $this->categoryRepository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $this->controller->destroy(1);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Database error'
        ], $response->getData(true));
    }
}