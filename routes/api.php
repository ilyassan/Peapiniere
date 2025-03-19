<?php

use App\Http\Controllers\PlantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix("plants")->group(function () {
    Route::get("/", [PlantController::class, "index"]);
    Route::post("/", [PlantController::class, "store"]);
    Route::get("/{id}", [PlantController::class, "show"]);
    Route::put("/{id}", [PlantController::class, "update"]);
    // Route::delete("/{plant}", [PlantController::class, "destroy"]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
