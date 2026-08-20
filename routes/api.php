<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Drive\FileController;
use App\Http\Controllers\Engagement\ActivityController;
use App\Http\Controllers\Engagement\EventParticipationController;
use App\Http\Controllers\Engagement\SavedPostController;
use App\Http\Controllers\Institution\InstitutionApplicationController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Statistics\StatisticsController;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // Create a regular User account and return its JWT.
    Route::post('/register', [AuthController::class, 'register']);

    // Authenticate email/password and return a JWT.
    Route::post('/login', [AuthController::class, 'login']);

    // Email a password-reset link to an existing User.
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink']);

    // Replace a password using the emailed reset token.
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.reset');

    Route::middleware('auth:api')->group(function () {
        // Return the current User with profile picture and Institution profile.
        Route::get('/me', [AuthController::class, 'me']);

        // Partially update the current User's name or email.
        Route::patch('/me', [AuthController::class, 'updateUserProfile']);

        // Return the current User's Interested, Going, and saved Posts.
        Route::get('/me/activity', [ActivityController::class, 'show']);

        // Replace the current JWT with a newly issued token.
        Route::post('/refresh', [AuthController::class, 'refreshToken']);

        // Invalidate the current JWT.
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated API
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    // Submit the current User's organization data and set their request to PENDING.
    Route::post('/institution/application', [InstitutionApplicationController::class, 'store']);

    // Return the current User's Institution application and profile.
    Route::get('/institution/application', [InstitutionApplicationController::class, 'show']);

    // Partially update the current User's Institution profile.
    Route::patch('/institution/application', [InstitutionApplicationController::class, 'update']);

    // List all Categories; every authenticated role may read them.
    Route::get('/categories', [CategoryController::class, 'index']);

    // Create a Category; CategoryPolicy permits Admins only.
    Route::post('/categories', [CategoryController::class, 'store'])
        ->can('create', Category::class);

    // Return one Category by route-bound ID.
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Partially update a Category; CategoryPolicy permits Admins only.
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])
        ->can('update', 'category');

    // Delete a Category; CategoryPolicy permits Admins only.
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->can('delete', 'category');

    // List all Posts; defaults to latest and accepts ?sort=random.
    Route::get('/posts', [PostController::class, 'feed']);

    // List all Posts published by the selected Institution User.
    Route::get('/institutions/{institution}/posts', [PostController::class, 'institutionPosts']);

    // List all Posts in the selected Category.
    Route::get('/categories/{category}/posts', [PostController::class, 'index']);

    // Create a Post in this Category; only approved Institutions may create.
    Route::post('/categories/{category}/posts', [PostController::class, 'store'])
        ->can('create', Post::class);

    // Scoped bindings ensure the Post really belongs to the Category in the URL.
    Route::prefix('categories/{category}/posts/{post}')
        ->scopeBindings()
        ->group(function () {
            // Return this Post only when it belongs to the Category in the URL.
            Route::get('/', [PostController::class, 'show']);

            // Partially update an owned Post; Admins may also moderate it.
            Route::patch('/', [PostController::class, 'update'])
                ->can('update', 'post');

            // Delete an owned Post; Admins may also moderate it.
            Route::delete('/', [PostController::class, 'destroy'])
                ->can('delete', 'post');

            // Create or replace the current User's INTERESTED/GOING Event status.
            Route::put('/participation', [EventParticipationController::class, 'set']);

            // Remove the current User's Event participation status.
            Route::delete('/participation', [EventParticipationController::class, 'remove']);

            // Idempotently save this Post for the current User.
            Route::put('/save', [SavedPostController::class, 'save']);

            // Remove this Post from the current User's saved Posts.
            Route::delete('/save', [SavedPostController::class, 'unsave']);
        });

    // Role middleware protects dashboard areas; services add approval checks.
    Route::prefix('institution')
        ->middleware('role:INSTITUTION')
        ->group(function () {
            // Return aggregate engagement totals for the current approved Institution.
            Route::get('/statistics', [StatisticsController::class, 'institution']);
        });

    Route::prefix('admin')
        ->middleware('role:ADMIN')
        ->group(function () {
            // Return platform-wide User, Post, and engagement totals.
            Route::get('/statistics', [StatisticsController::class, 'platform']);

            // List Users whose Institution application is currently PENDING.
            Route::get('/institution-applications', [InstitutionApplicationController::class, 'index']);

            // Change one pending application to APPROVED or REJECTED.
            Route::patch('/institution-applications/{applicant}', [InstitutionApplicationController::class, 'review']);
        });

    Route::prefix('drive')->group(function () {
        // List files uploaded by the current User.
        Route::get('/', [FileController::class, 'index']);

        // Upload and attach a file after authorizing the target model.
        Route::post('/upload', [FileController::class, 'store']);

        // Permanently delete a file uploaded by the current User.
        Route::delete('/{id}', [FileController::class, 'destroy']);
    });
});
