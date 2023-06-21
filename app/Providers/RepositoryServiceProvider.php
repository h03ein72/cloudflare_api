<?php

namespace App\Providers;

use App\Interfaces\CloudflareInterface;
use App\Repositories\CloudflareRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CloudflareInterface::class, CloudflareRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
