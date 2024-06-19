<?php

namespace App\Http\Resources;

use App\Helpers\NumberToWordsHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'serial_number' => $this->serial_number,
            'user' => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'position' => $this->position,
            'status' => $this->status,
            'discount' => $this->discount,
            'delivery' => $this->getDeliveryCode($this->delivery),
            'updated_at' => Jalalian::fromCarbon($this->updated_at)->format('Y/m/d'),
            'items' => TypeItemResource::collection($this->whenLoaded('typeItems')),
            'aggregated_items' => AggregatedItemResource::collection($this->whenLoaded('aggregatedItems')),
            'amount_payable' => number_format($this->amount_payable),
            'amount_payable_letters' =>NumberToWordsHelper::convertNumberToWords($this->amount_payable)

        ];
    }
    private function getDeliveryCode($delivery)
    {
        switch ($delivery) {
            case 'location':
                return 1;
            case 'factory':
                return 2;
            default:
                return null; // یا هر مقداری که در صورت ناشناخته بودن `delivery` مدنظر شماست
        }
        }
}

