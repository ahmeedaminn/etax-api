<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Drive\FileController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Post\PostController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION DOMAIN (/api/auth/...)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    // 1. PUBLIC ROUTES
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

    // 2. PROTECTED ROUTES
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

/*
|--------------------------------------------------------------------------
| CONTENT DOMAIN (Categories & Posts)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // --- CATEGORIES ---
    Route::get('/categories', [CategoryController::class, 'index']);           // List all categories (for Dashboard)
    Route::post('/categories', [CategoryController::class, 'store']);          // Create a new category
    Route::get('/categories/{category}', [CategoryController::class, 'show']); // View a single category

    // --- NESTED POSTS ---
    Route::get('/categories/{category}/posts', [PostController::class, 'index']);  // List all posts inside a specific category
    Route::post('/categories/{category}/posts', [PostController::class, 'store']); // Create a new post inside a specific category
    Route::get('/categories/{category}/posts/{post}', [PostController::class, 'show']); // this is line I added, see the id for the post is dynamic and this laravel takes and inject it into the show function inside the controller
    // --- STANDALONE POSTS (For later) ---
    // Editing or deleting a post usually doesn't need the category ID in the URL 
    // because the Post ID is already globally unique.
    // Route::get('/posts/{post}', [PostController::class, 'show']);
    // Route::put('/posts/{post}', [PostController::class, 'update']);
    // Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});


/*
|--------------------------------------------------------------------------
| DRIVE DOMAIN (/api/drive/...)
|--------------------------------------------------------------------------
*/
Route::prefix('drive')->group(function () {
    Route::middleware('auth:api')->group(function () {
        
        Route::get('/', [FileController::class, 'index']);           // GET /api/drive
        Route::post('/upload', [FileController::class, 'store']);    // POST /api/drive/upload
        Route::delete('/{id}', [FileController::class, 'destroy']);  // DELETE /api/drive/5
        
    });
});