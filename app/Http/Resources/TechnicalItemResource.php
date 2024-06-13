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
            'map_dimension' => $this->map_dimension,
            'map_view' => $this->map_view,
            'usage' => $this->usage,
        ];
    }
}
