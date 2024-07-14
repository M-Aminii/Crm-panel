<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FinalOrderItem;
use Carbon\Carbon;

class UpdateDeliveryDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // دریافت آیتم‌های تحویلی
        $deliveryItems = FinalOrderItem::all();

        foreach ($deliveryItems as $item) {
            $remainingDays = (int) $item->delivery_date;

            if ($remainingDays > 0) {
                // محاسبه روز بعدی که روز کاری باشد
                $nextBusinessDay = Carbon::now()->nextBusinessDay();

                // اگر روز بعدی روز کاری است، کاهش یک روز از remaining_days
                if ($nextBusinessDay->isBusinessDay()) {
                    $remainingDays -= 1;
                    $item->delivery_date = (string) $remainingDays;
                    $item->save();
                }
            }
        }
    }
}


