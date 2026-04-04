<?php

namespace App\Providers;


use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;

use App\Models\Restaurant;
use App\Observers\RestaurantObserver;
use App\Models\News;
use App\Observers\NewsObserver;
use App\Support\MonitoringAccess;

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
        $this->configureMonitoringAccess();
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

    /**
     * Регистрирует доступ к служебным dashboard-страницам только для root.
     */
    protected function configureMonitoringAccess(): void
    {
        Gate::define('viewPulse', fn (User $user) => MonitoringAccess::canViewDashboard($user));
    }
}
