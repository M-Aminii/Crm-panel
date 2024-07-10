<?php

namespace App\Http\Resources;

use App\Helpers\NumberToWordsHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleInvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'serial_number' => $this->serial_number,
            'description' => $this->description,
            'status' => $this->status,
            'pre_payment' => $this->pre_payment,
            'before_delivery' => $this->before_delivery,
            'cheque' => $this->cheque,
            'amount_payable' => number_format($this->amount_payable),
            'amount_payable_letters' =>NumberToWordsHelper::convertNumberToWords($this->amount_payable)
        ];
    }
}
