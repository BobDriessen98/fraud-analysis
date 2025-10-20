<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'telephone' => $this->phone_number,
            'iban' => $this->iban,
            'ip_address' => $this->ip_address,
            'fraud_reasons' => FraudReasonResource::collection($this->whenLoaded('fraudReasons')),
        ];
    }
}
