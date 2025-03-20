<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
    Route::delete("/{id}", [OrderController::class, "destroy"]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
