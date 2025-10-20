<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerRepository
{
    public function storeCustomerCollection(Collection $customers): Collection
    {
        $validCustomers = $this->filterValidCustomers($customers);
        $savedCustomers = collect();

        foreach ($validCustomers as $data) {
            $savedCustomers->push(
                Customer::updateOrCreate(
                    ['customer_id' => $data['customerId']],
                    [
                        'bsn' => $data['bsn'] ?? null,
                        'first_name' => $data['firstName'] ?? null,
                        'last_name' => $data['lastName'] ?? null,
                        'date_of_birth' => $data['dateOfBirth'] ?? null,
                        'phone_number' => $data['phoneNumber'] ?? null,
                        'email' => $data['email'] ?? null,
                        'tag' => $data['tag'] ?? null,
                        'address_street' => $data['address']['street'] ?? null,
                        'address_postcode' => $data['address']['postcode'] ?? null,
                        'address_city' => $data['address']['city'] ?? null,
                        'ip_address' => $data['ipAddress'] ?? null,
                        'iban' => $data['iban'] ?? null,
                        'last_invoice_date' => $data['lastInvoiceDate'] ?? null,
                        'last_login_date_time' => $data['lastLoginDateTime'] ?? null,
                    ]
                )
            );
        }

        return $savedCustomers;
    }

    /**
     * Validate a collection of customers. If one or more validation rules fail for a customer, we omit this customer
     * from the collection
     * @param Collection $customers
     * @return Collection
     */
    private function filterValidCustomers(Collection $customers): Collection
    {
        $rules = [
            'customerId' => 'required|integer',
            'bsn' => 'nullable|integer',
            'firstName' => 'nullable|string',
            'lastName' => 'nullable|string',
            'dateOfBirth' => 'nullable|date_format:d-m-Y',
            'phoneNumber' => 'nullable|string',
            'email' => 'nullable|email',
            'tag' => 'nullable|string',
            'address.street' => 'nullable|string',
            'address.postcode' => 'nullable|string',
            'address.city' => 'nullable|string',
            'ipAddress' => 'nullable|ip',
            'iban' => ['nullable', 'string'],
            'lastInvoiceDate' => 'nullable|date_format:d-m-Y',
            'lastLoginDateTime' => 'nullable|date_format:d-m-Y H:i:s',
        ];

        return $customers->filter(function ($customer) use ($rules) {
            $validator = Validator::make($customer, $rules);

            if ($validator->fails()) {
                Log::warning("Invalid customer data", [
                    'errors' => $validator->errors()->toArray(),
                    'data' => $customer,
                ]);
                return false;
            }

            return true;
        })->values();
    }

}
