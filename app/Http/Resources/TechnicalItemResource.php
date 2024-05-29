<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TechnicalItemResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'edge_type' => $this->edge_type,
            'glue_type' => $this->glue_type,
            'post_type' => $this->post_type,
            'delivery_date' => $this->delivery_date,
            'frame' => $this->frame,
            'balance' => $this->balance,
            'vault_type' => $this->vault_type,
            'part_number' => $this->part_number,
            'map_dimension' => $this->map_dimension,
            'map_view' => $this->map_view,
            'vault_number' => $this->vault_number,
            'delivery_meterage' => $this->delivery_meterage,
            'order_number' => $this->order_number,
            'usage' => $this->usage,
            'car_type' => $this->car_type,
        ];
    }
}
