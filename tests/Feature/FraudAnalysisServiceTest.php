<?php

use App\Enums\FraudReasonCode;
use App\Models\Scan;
use App\Services\Internal\FraudScanningService;
use App\Services\External\CustomerApiService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('Runs a fraud scan', function () {
    $mockedApiService = Mockery::mock(CustomerApiService::class);

    $mockedCustomers = collect([
        //same ip and iban
        [
            'customerId' => 1,
            'dateOfBirth' => Carbon::now()->subYears(25)->format('d-m-Y'),
            'phoneNumber' => '+31612345678',
            'iban' => 'IBAN123',
            'ipAddress' => '1.1.1.1'
        ],
        [
            'customerId' => 2,
            'dateOfBirth' => Carbon::now()->subYears(25)->format('d-m-Y'),
            'phoneNumber' => '+31612345678',
            'iban' => 'IBAN123',
            'ipAddress' => '1.1.1.1'
        ],
        //underage
        [
            'customerId' => 3,
            'dateOfBirth' => Carbon::now()->subYears(16)->format('d-m-Y'),
            'phoneNumber' => '+31612345678',
            'iban' => 'IBAN234',
            'ipAddress' => '1.1.1.2'
        ],
        //foreign phone
        [
            'customerId' => 4,
            'dateOfBirth' => Carbon::now()->subYears(25)->format('d-m-Y'),
            'phoneNumber' => '+41612345678',
            'iban' => 'IBAN345',
            'ipAddress' => '1.1.1.3'
        ],
        //no fraud
        [
            'customerId' => 5,
            'dateOfBirth' => Carbon::now()->subYears(25)->format('d-m-Y'),
            'phoneNumber' => '+31612345678',
            'iban' => 'IBAN456',
            'ipAddress' => '1.1.1.4'
        ],
    ]);

    $mockedApiService->shouldReceive('getCustomers')
        ->andReturn($mockedCustomers);
    $this->app->instance(CustomerApiService::class, $mockedApiService);
    $service = resolve(FraudScanningService::class);

    $scan = $service->fraudScan();

    expect($scan)
        ->toBeInstanceOf(Scan::class)
        ->and($scan->customers)->toHaveCount(5);

    $customers = $scan->customers;

    expect($customers[0]->fraudReasons[0]->code)->toEqual(FraudReasonCode::DUPLICATE_IBAN->value);
    expect($customers[0]->fraudReasons[1]->code)->toEqual(FraudReasonCode::DUPLICATE_IP_ADDRESS->value);
    expect($customers[1]->fraudReasons[0]->code)->toEqual(FraudReasonCode::DUPLICATE_IBAN->value);
    expect($customers[1]->fraudReasons[1]->code)->toEqual(FraudReasonCode::DUPLICATE_IP_ADDRESS->value);
    expect($customers[2]->fraudReasons[0]->code)->toEqual(FraudReasonCode::UNDERAGE_CUSTOMER->value);
    expect($customers[3]->fraudReasons[0]->code)->toEqual(FraudReasonCode::FOREIGN_PHONE_NUMBER->value);
    expect($customers[4]->fraudReasons)->toBeEmpty();

    $cachedScan = Cache::get('latest_scan');
    expect($cachedScan->id)->toBe($scan->id);
});
