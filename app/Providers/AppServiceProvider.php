<?php

namespace App\Providers;

use App\Repositories\Eloquent\Auth\UserRepository;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Eloquent\Drive\FileRepository;
use App\Repositories\Eloquent\Engagement\EventParticipationRepository;
use App\Repositories\Eloquent\Engagement\SavedPostRepository;
use App\Repositories\Eloquent\Institution\InstitutionProfileRepository;
use App\Repositories\Eloquent\Post\PostRepository;
use App\Repositories\Eloquent\Statistics\StatisticsRepository;
use App\Repositories\Interfaces\Auth\UserRepositoryInterface;
use App\Repositories\Interfaces\Category\CategoryRepositoryInterface;
use App\Repositories\Interfaces\Drive\FileRepositoryInterface;
use App\Repositories\Interfaces\Engagement\EventParticipationRepositoryInterface;
use App\Repositories\Interfaces\Engagement\SavedPostRepositoryInterface;
use App\Repositories\Interfaces\Institution\InstitutionProfileRepositoryInterface;
use App\Repositories\Interfaces\Post\PostRepositoryInterface;
use App\Repositories\Interfaces\Statistics\StatisticsRepositoryInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(
            InstitutionProfileRepositoryInterface::class,
            InstitutionProfileRepository::class,
        );
        $this->app->bind(
            EventParticipationRepositoryInterface::class,
            EventParticipationRepository::class,
        );
        $this->app->bind(SavedPostRepositoryInterface::class, SavedPostRepository::class);
        $this->app->bind(StatisticsRepositoryInterface::class, StatisticsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Direct the password reset email link to your React app URL
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return 'http://localhost:5173/reset-password?token='.$token.'&email='.$notifiable->getEmailForPasswordReset();
        });
    }
}
