<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FraudReasonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
            'context' => $this->whenPivotLoaded('customer_fraud_reasons', fn() => $this->pivot->context),
        ];
    }
}
