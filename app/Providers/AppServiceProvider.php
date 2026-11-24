<?php

namespace App\Providers;

use App\Models\Athlete;
use App\Models\Budget;
use App\Models\Inspection;
use App\Models\MaintenanceRecord;
use App\Models\MedalHistory;
use App\Models\Venue;
use App\Models\YouthOrg;
use App\Observers\AuditObserver;
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
        Venue::observe(AuditObserver::class);
        Athlete::observe(AuditObserver::class);
        MedalHistory::observe(AuditObserver::class);
        Budget::observe(AuditObserver::class);
        YouthOrg::observe(AuditObserver::class);
        Inspection::observe(AuditObserver::class);
        MaintenanceRecord::observe(AuditObserver::class);
    }
}
