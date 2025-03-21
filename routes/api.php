<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get("/statistics", [DashboardController::class, "index"]);

Route::prefix("plants")->group(function () {
    Route::get("/", [PlantController::class, "index"]);
    Route::post("/", [PlantController::class, "store"]);
    Route::get("/{id}", [PlantController::class, "show"]);
    Route::put("/{id}", [PlantController::class, "update"]);
    Route::delete("/{id}", [PlantController::class, "destroy"]);
});

Route::prefix("orders")->group(function () {
    Route::get("/", [OrderController::class, "index"]);
    Route::post("/", [OrderController::class, "store"]);
    Route::get("/{id}", [OrderController::class, "show"]);
    Route::put("/{id}", [OrderController::class, "update"]);
});

Route::prefix("categories")->group(function () {
    Route::get("/", [CategoryController::class, "index"]);
    Route::post("/", [CategoryController::class, "store"]);
    Route::get("/{id}", [CategoryController::class, "show"]);
    Route::put("/{id}", [CategoryController::class, "update"]);
    Route::delete("/{id}", [CategoryController::class, "destroy"]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
