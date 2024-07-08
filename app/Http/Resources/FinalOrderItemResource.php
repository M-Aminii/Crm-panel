<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class FinalOrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->key,
            'product' => new ProductResource($this->whenLoaded('product')),
            'type_id' => $this->type_id,
            'area' => $this->area,
        ];
    }
}
//'amount_payable' => number_format($this->amount_payable),
//'amount_payable_letters' =>NumberToWordsHelper::convertNumberToWords($this->amount_payable)
