<?php

namespace App\Providers;

use App\Observers\SearchIndexObserver;
use App\Search\SearchIndexer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (SearchIndexer::MODELS as $model) {
            $model::observe(SearchIndexObserver::class);
        }
    }
}
