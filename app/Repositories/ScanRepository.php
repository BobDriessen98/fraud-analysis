<?php

namespace App\Repositories;

use App\Models\Scan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScanRepository
{
    public function createScanAndAttachCustomers(Collection $customers): Scan{
        $scan = Scan::create(['occurred_at' => Carbon::now()]);
        $scan->customers()->attach($customers->pluck('id'));

        return $scan;
    }
}
