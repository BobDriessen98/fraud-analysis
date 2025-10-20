<?php

namespace App\Repositories;

use App\Enums\FraudReasonCode;
use App\Models\FraudReason;

class FraudReasonRepository
{
    public function fraudReasonByCode(FraudReasonCode $code): FraudReason{
        return FraudReason::whereCode($code)->first();
    }

    public function attachFraudReasonToCustomer(FraudReason $fraudReason, int $customerId, string $context): void
    {
        $fraudReason->customers()->attach(
            $customerId,
            ['context' => $context]
        );
    }
}
