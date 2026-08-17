<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\Auth\UserRepositoryInterface;
use App\Repositories\Eloquent\Auth\UserRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // Category Binding
        $this->app->bind(
            \App\Repositories\Interfaces\Category\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\Category\CategoryRepository::class
        );

        // Post Binding
        $this->app->bind(
            \App\Repositories\Interfaces\Post\PostRepositoryInterface::class,
            \App\Repositories\Eloquent\Post\PostRepository::class
        );

        // File Binding
        $this->app->bind(
            \App\Repositories\Interfaces\Drive\FileRepositoryInterface::class,
            \App\Repositories\Eloquent\Drive\FileRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Direct the password reset email link to your React app URL
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return 'http://localhost:5173/reset-password?token=' . $token . '&email=' . $notifiable->getEmailForPasswordReset();
        });
    }
}
