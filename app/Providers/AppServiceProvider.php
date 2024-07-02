<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\TemplateServiceInterface;
use App\Services\TemplateService;
use App\Repositories\Interfaces\TemplateRepositoryInterface;
use App\Repositories\TemplateRepository;


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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
