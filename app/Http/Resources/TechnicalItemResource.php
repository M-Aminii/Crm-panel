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
            'frame' => $this->translateBoolean($this->frame),
            'balance' => $this->translateBoolean($this->balance),
            'vault_type' => $this->vault_type,
            'map_dimension' => $this->translateBoolean($this->map_dimension),
            'map_view' => $this->translateBoolean($this->map_view),
            'usage' => $this->usage,
        ];
    }

    private function translateBoolean($value)
    {
        return $value == 1 ? 'دارد' : 'ندارد';
    }
}

