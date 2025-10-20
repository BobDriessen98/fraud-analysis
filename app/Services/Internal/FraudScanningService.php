<?php

namespace App\Services\Internal;

use App\Enums\FraudReasonCode;
use App\Models\Scan;
use App\Repositories\CustomerRepository;
use App\Repositories\FraudReasonRepository;
use App\Repositories\ScanRepository;
use App\Services\External\CustomerApiService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FraudScanningService
{

    public function __construct(
        protected CustomerApiService    $customerApiService,
        protected CustomerRepository    $customerRepository,
        protected FraudReasonRepository $fraudReasonRepository,
        protected ScanRepository        $scanRepository)
    {
    }

    /**
     * This function fetches a list of customers from the external API, creates or updates customer models and creates
     * a new scan object, to which all customers that were fetched are then attached. The function then checks all
     * customers for fraudulent activity and attaches fraud reasons to these customers if cases of fraud are found.
     * @return Scan|null
     */
    public function fraudScan(): ?Scan
    {
        $customersFromAPI = $this->customerApiService->getCustomers();

        if (!$customersFromAPI) {
            return null;
        }

        $savedCustomers = $this->customerRepository->storeCustomerCollection($customersFromAPI);

        $scan = $this->scanRepository->createScanAndAttachCustomers($savedCustomers);

        $this->checkDuplicateIBAN($savedCustomers);
        $this->checkDuplicateIPAddress($savedCustomers);
        $this->checkUnderageCustomer($savedCustomers);
        $this->checkForeignPhoneNumber($savedCustomers);

        $scan->loadMissing('customers.fraudReasons');

        Cache::put('latest_scan', $scan, Carbon::now()->addDay());

        return $scan;
    }

    private function checkDuplicateIBAN(Collection $customers): void
    {
        $fraudReason = $this->fraudReasonRepository->fraudReasonByCode(FraudReasonCode::DUPLICATE_IBAN);

        $duplicates = $customers
            ->groupBy('iban')
            ->filter(fn($group) => $group->count() > 1);

        foreach ($duplicates as $iban => $customersWithIBAN) {
            foreach ($customersWithIBAN as $customer) {
                $otherCustomerIds = $customersWithIBAN
                    ->where('id', '!=', $customer->id)
                    ->pluck('customer_id')
                    ->implode(', ');

                $context = "IBAN $iban is also used by customers: $otherCustomerIds";

                $this->fraudReasonRepository->attachFraudReasonToCustomer(
                    $fraudReason,
                    $customer->id,
                    $context
                );
            }
        }
    }

    private function checkDuplicateIPAddress(Collection $customers): void
    {
        $fraudReason = $this->fraudReasonRepository->fraudReasonByCode(FraudReasonCode::DUPLICATE_IP_ADDRESS);

        $duplicates = $customers
            ->groupBy('ip_address')
            ->filter(fn($group) => $group->count() > 1);

        foreach ($duplicates as $ipAddress => $customersWithIPAddress) {
            foreach ($customersWithIPAddress as $customer) {
                $otherCustomerIds = $customersWithIPAddress
                    ->where('id', '!=', $customer->id)
                    ->pluck('customer_id')
                    ->implode(', ');

                $context = "IP Address $ipAddress is also used by customers: $otherCustomerIds";

                $this->fraudReasonRepository->attachFraudReasonToCustomer(
                    $fraudReason,
                    $customer->id,
                    $context
                );
            }
        }
    }

    private function checkUnderageCustomer(Collection $customers): void
    {
        $fraudReason = $this->fraudReasonRepository->fraudReasonByCode(FraudReasonCode::UNDERAGE_CUSTOMER);

        $underageCustomers = $customers->filter(fn($c) => $c->date_of_birth->age < 18);

        foreach ($underageCustomers as $customer) {
            $age = $customer->date_of_birth->age;
            $context = "Customer age is $age";
            $this->fraudReasonRepository->attachFraudReasonToCustomer($fraudReason, $customer->id, $context);
        }
    }

    private function checkForeignPhoneNumber(Collection $customers): void
    {
        $fraudReason = $this->fraudReasonRepository->fraudReasonByCode(FraudReasonCode::FOREIGN_PHONE_NUMBER);

        $foreignCustomers = $customers->filter(fn($c) => !str_starts_with($c->phone_number, '+31'));

        foreach ($foreignCustomers as $customer) {
            $context = "Phone number {$customer->phone_number} is not Dutch";
            $this->fraudReasonRepository->attachFraudReasonToCustomer($fraudReason, $customer->id, $context);
        }
    }
}
