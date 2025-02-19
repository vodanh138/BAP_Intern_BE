<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\TemplateServiceInterface;
use App\Services\TemplateService;
use App\Repositories\Interfaces\TemplateRepositoryInterface;
use App\Repositories\TemplateRepository;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\ShowRepositoryInterface;
use App\Repositories\ShowRepository;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Repositories\SectionRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\RoleRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(TemplateServiceInterface::class, TemplateService::class);
        $this->app->singleton(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(ShowRepositoryInterface::class, ShowRepository::class);
        $this->app->singleton(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->singleton(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->singleton(RepositoryInterface::class, BaseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
