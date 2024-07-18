<?php

namespace App\Http\Resources;

use App\Helpers\NumberToWordsHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        $totalArea = 0;
        $totalQuantity = 0;
        $totalWeight = 0;

        foreach ($this->aggregatedItems as $item) {
            $totalArea += $item->total_area;
            $totalQuantity += $item->total_quantity;
            $totalWeight += $item->total_weight;
        }


        return [
            'serial_number' => $this->serial_number,
            'user' => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'description' => $this->description,
            'status' => $this->status,
            'informal_status'=>$this->informal_status,
            'discount' => $this->discount,
            'pre_payment' => $this->pre_payment,
            'before_delivery' => $this->before_delivery,
            'cheque' => $this->cheque,
            'delivery' => $this->getDeliveryCode($this->delivery),
            'updated_at' => Jalalian::fromCarbon($this->updated_at)->format('Y/m/d'),
            'items' => TypeItemResource::collection($this->whenLoaded('typeItems')),
            'aggregated_items' => AggregatedItemResource::collection($this->whenLoaded('aggregatedItems')),
            'amount_payable' => number_format($this->amount_payable),
            'amount_payable_letters' =>NumberToWordsHelper::convertNumberToWords($this->amount_payable),

            'total_area_sum' => $totalArea,
            'total_quantity_sum' => $totalQuantity,
            'total_weight_sum'=>$totalWeight
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

