<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\Restaurant;
use App\Observers\RestaurantObserver;

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
        $this->configureModels();
        Restaurant::observe(RestaurantObserver::class);
    }

    /**
     * Configure model settings.
     */
    protected function configureModels(): void
    {
        DB::disableQueryLog();  
        Model::preventLazyLoading(true);
    }
}
