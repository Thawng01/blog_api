<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// authentication && users
Route::post("/users/new", [UserController::class, 'create']);
Route::post("/auth/login", [UserController::class, 'login']);
Route::post("/auth/logout", [UserController::class, 'logout']);
Route::get("/users", [UserController::class, "index"]);

// category
Route::get('/categories', [CategoryController::class, 'index']);

// blogs
Route::get("/blogs", [BlogController::class, "index"]);
Route::get("/blogs/{id}", [BlogController::class, "get_blog_by_id"]);
Route::post("/blogs/view/{id}", [BlogController::class, "update_view_count"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/blogs/create", [BlogController::class, "create"]);
    Route::delete("blogs/{id}", [BlogController::class, 'delete']);
    Route::post("/blogs/update/{id}", [BlogController::class, 'update_blog']);
    Route::post("/categories/create", [CategoryController::class, 'create']);
});