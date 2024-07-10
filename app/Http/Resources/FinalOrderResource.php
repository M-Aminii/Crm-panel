<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class FinalOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice' => new SimpleInvoiceResource($this->whenLoaded('invoice')),
            'user' => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'serial_number' => $this->serial_number,
            'delivery_date' => $this->delivery_date,
            'sent_to_factory' => $this->sent_to_factory,
            'sent_to_customer' => $this->sent_to_customer,
            'informal_invoice_date' => Jalalian::fromCarbon(Carbon::parse($this->informal_invoice_date))->format('Y/m/d'),
            'formal_invoice_date' => Jalalian::fromCarbon(Carbon::parse($this->formal_invoice_date))->format('Y/m/d'),
            'delivery_time' => $this->delivery_time,
            'financial_approval_date' => $this->financial_approval_date,
            'pdf_map' => $this->pdf_map,
            'cad_map' => $this->cad_map,
            'pdf_dimension' => $this->pdf_dimension,
            'xml_dimension' => $this->xml_dimension,
            'items' => FinalOrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
//'amount_payable' => number_format($this->amount_payable),
//'amount_payable_letters' =>NumberToWordsHelper::convertNumberToWords($this->amount_payable)
