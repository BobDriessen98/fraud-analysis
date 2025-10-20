<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'occurred_at' => $this->occurred_at,
            'customers' => CustomerResource::collection($this->whenLoaded('customers')),
        ];
    }
}
