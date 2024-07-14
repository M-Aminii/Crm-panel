<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\FinalOrder;
use App\Observers\FinalOrderObserver;
use App\Observers\InvoiceObserver;
use Cmixin\BusinessDay;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Invoice::observe(InvoiceObserver::class);
        FinalOrder::observe(FinalOrderObserver::class);
        // فعال‌سازی روزهای کاری برای کلاس Carbon و تنظیم منطقه زمانی به ایران
        BusinessDay::enable(Carbon::class, 'ir');

        // افزودن تعطیلات رسمی ایران (می‌توانید تاریخ‌ها را مطابق نیاز خود تنظیم کنید)
        Carbon::setHolidaysRegion('ir');

    }
}
