<?php
namespace App\Observers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\FinalOrder;
use App\Models\TechnicalItem;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function updated(Invoice $invoice)
    {

        if ($invoice->status === InvoiceStatus::Formal) {
            $technicalItem = TechnicalItem::where('invoice_id', $invoice->id)->first();
            //dd($technicalItem->delivery_date);
            FinalOrder::updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'user_id' => $invoice->user_id,
                    'customer_id' => $invoice->customer_id,
                    'serial_number' => $invoice->serial_number,
                    'delivery_date' => $invoice->created_at, // این را می‌توانید تغییر دهید
                    'informal_invoice_date' => $invoice->getOriginal('updated_at'),
                    'formal_invoice_date' => $invoice->updated_at,
                    'delivery_time' => 25, // فرض کنید که مقدار ثابت است
                    'pre_payment' => $invoice->pre_payment,
                    'before_delivery' => $invoice->before_delivery,
                    'cheque' => $invoice->cheque
                ]
            );
        } else {
            FinalOrder::where('invoice_id', $invoice->id)->delete();
        }
    }
}
