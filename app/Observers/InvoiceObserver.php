<?php
namespace App\Observers;

use App\Enums\InvoiceStatus;
use App\Models\AggregatedItem;
use App\Models\Invoice;
use App\Models\FinalOrder;
use App\Models\TechnicalItem;
use App\Models\TypeItem;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function updated(Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::Formal) {
            $technicalItem = TechnicalItem::where('invoice_id', $invoice->id)->first();

            // به‌روزرسانی یا ایجاد رکورد در final_orders
            $finalOrder = FinalOrder::updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'user_id' => $invoice->user_id,
                    'customer_id' => $invoice->customer_id,
                    'serial_number' => $invoice->serial_number,
                    'delivery_date' => $invoice->created_at,
                    'informal_invoice_date' => $invoice->getOriginal('updated_at'),
                    'formal_invoice_date' => $invoice->updated_at,
                    'delivery_time' => 25,
                ]
            );

            // حذف آیتم‌های قبلی
            $finalOrder->items()->delete();

            // محاسبه مجموع متراژها برای هر محصول
            $aggregatedItems = AggregatedItem::where('invoice_id', $invoice->id)->get();
            $productAreas = [];
            $keyCounter = 1; // شروع شمارنده کلید

            foreach ($aggregatedItems as $aggregatedItem) {
                $typeItem = TypeItem::find($aggregatedItem->type_id);
                $productId = $typeItem->product_id;
                $typeId = $aggregatedItem->type_id;

                if (!isset($productAreas[$productId])) {
                    $productAreas[$productId] = [
                        'total_area' => 0,
                        'type_id' => $typeId,
                        'key' => $keyCounter++ // تنظیم کلید و افزایش شمارنده
                    ];
                }
                $productAreas[$productId]['total_area'] += $aggregatedItem->total_area;
            }

            // ایجاد رکوردهای جدید در final_order_items
            foreach ($productAreas as $productId => $data) {
                $finalOrder->items()->create([
                    'product_id' => $productId,
                    'area' => $data['total_area'],
                    'type_id' => $data['type_id'],
                    'key' => $data['key'], // اطمینان از مقداردهی key
                ]);
            }
        } else {
            FinalOrder::where('invoice_id', $invoice->id)->delete();
        }
    }
}


