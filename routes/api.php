<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlantController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware("jwt.auth")->group(function(){
    Route::get("/statistics", [DashboardController::class, "index"])->middleware("admin");

    Route::prefix("plants")->group(function () {
        Route::get("/", [PlantController::class, "index"]);
        Route::post("/", [PlantController::class, "store"])->middleware("admin");
        Route::get("/{slug}", [PlantController::class, "show"]);
        Route::put("/{slug}", [PlantController::class, "update"])->middleware("admin");
        Route::delete("/{slug}", [PlantController::class, "destroy"])->middleware("admin");
    });

    Route::prefix("orders")->group(function () {
        Route::get("/", [OrderController::class, "index"]);
        Route::post("/", [OrderController::class, "store"])->middleware("client");
        Route::get("/{id}", [OrderController::class, "show"]);
        Route::put("/{id}", [OrderController::class, "update"]);
    });

    Route::prefix("categories")->group(function () {
        Route::get("/", [CategoryController::class, "index"]);
        Route::post("/", [CategoryController::class, "store"]);
        Route::get("/{id}", [CategoryController::class, "show"]);
        Route::put("/{id}", [CategoryController::class, "update"]);
        Route::delete("/{id}", [CategoryController::class, "destroy"]);
    })->middleware("admin");
});

Route::prefix("auth")->group(function () {
    Route::post("/signup", [AuthController::class, "signup"]);
    Route::post("/login", [AuthController::class, "login"]);
})->middleware("jwt.guest");

Route::post("/logout", [AuthController::class, "logout"]);

Route::get('/user', function () {
    return Auth::user();
})->middleware('jwt.auth');
