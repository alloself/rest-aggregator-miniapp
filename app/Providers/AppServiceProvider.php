<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Models\Restaurant;
use App\Observers\RestaurantObserver;
use App\Models\News;
use App\Observers\NewsObserver;

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
        JsonResource::withoutWrapping();
        Restaurant::observe(RestaurantObserver::class);
        News::observe(NewsObserver::class);
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
