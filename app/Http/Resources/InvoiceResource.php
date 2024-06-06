<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        foreach ($this->aggregatedItems as $aggregatedItems) {
            $this->total_price += $aggregatedItems->total_price;
        }

        return [
            'serial_number' => $this->serial_number,
            'user' => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'position' => $this->position,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'items' => TypeItemResource::collection($this->whenLoaded('typeItems')),
            'aggregated_items' => AggregatedItemResource::collection($this->whenLoaded('aggregatedItems')),
            'amount_payable' => number_format($this->total_price)
        ];
    }
}

