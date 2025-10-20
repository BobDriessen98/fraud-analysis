<?php

namespace App\Services\External;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerApiService
{
    /**
     * Fetch a list of customers from the external API
     * @return Collection|null
     */
    public function getCustomers(): ?Collection
    {
        $response = Http::get(config('api.customers'));

        if ($response->failed()) {
            Log::warning('HTTP request to fetch customers failed', ['status' => $response->status()]);
            return null;
        }

        $result = $response->json();

        if (($result['success'] ?? false) && isset($result['customers'])) {
            return collect($result['customers']);
        }

        Log::warning('Customer API returned unsuccessful response', ['response' => $result]);
        return null;
    }
}
