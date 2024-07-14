<?php

namespace App\Observers;

use App\Jobs\UpdateDeliveryDates;
use App\Models\FinalOrder;

class FinalOrderObserver
{
    /**
     * Handle the FinalOrder "created" event.
     */
    public function created(FinalOrder $finalOrder): void
    {
        //
    }

    /**
     * Handle the FinalOrder "updated" event.
     */
    public function updated(FinalOrder $finalOrder)
    {

        // فقط زمانی که financial_approval_date آپدیت شده باشد
        if ($finalOrder->isDirty('financial_approval_date') && $finalOrder->financial_approval_date) {

            // اجرای job برای به‌روزرسانی روزهای کاری
            UpdateDeliveryDates::dispatch($finalOrder);
        }
    }

    /**
     * Handle the FinalOrder "deleted" event.
     */
    public function deleted(FinalOrder $finalOrder): void
    {
        //
    }

    /**
     * Handle the FinalOrder "restored" event.
     */
    public function restored(FinalOrder $finalOrder): void
    {
        //
    }

    /**
     * Handle the FinalOrder "force deleted" event.
     */
    public function forceDeleted(FinalOrder $finalOrder): void
    {
        //
    }
}
