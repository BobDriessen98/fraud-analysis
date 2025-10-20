<?php

namespace Database\Seeders;

use App\Enums\FraudReasonCode;
use App\Models\FraudReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedFraudReasons();
    }

    private function seedFraudReasons(): void
    {
        FraudReason::create([
            'code' => FraudReasonCode::DUPLICATE_IBAN,
            'description' => 'Duplicate IBAN',
        ]);
        FraudReason::create([
            'code' => FraudReasonCode::DUPLICATE_IP_ADDRESS,
            'description' => 'Duplicate IP Address',
        ]);
        FraudReason::create([
            'code' => FraudReasonCode::FOREIGN_PHONE_NUMBER,
            'description' => 'Foreign phone number',
        ]);
        FraudReason::create([
            'code' => FraudReasonCode::UNDERAGE_CUSTOMER,
            'description' => 'Underage customer',
        ]);
    }
}
