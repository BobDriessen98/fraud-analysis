<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\Internal\FraudScanningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ScanViewController extends Controller
{
    public function __construct(protected FraudScanningService $fraudScanningService)
    {
    }

    /**
     * Shows a list of all scans that have occurred
     * @return View
     */
    public function index(): View {
        $scans = Scan::orderByDesc('occurred_at')->get();
        return view('scans.all_scans', ['scans' => $scans]);
    }

    /**
     * Fetch the latest scan from the cache and show this in the scan details view
     * @return View
     */
    public function latestScan(): View
    {
        $scan = Cache::get('latest_scan');
        return view('scans.scan_details', ['scan' => $scan]);
    }

    /**
     * Fetch a scan by ID and show this in the scan details view
     * @param $scanId
     * @return View
     */
    public function show($scanId): View
    {
        $scan = Scan::with('customers.fraudReasons')->findOrFail($scanId);

        return view('scans.scan_details', ['scan' => $scan]);
    }

    /**
     * Runs a new scan and redirects the user to the latest scan page if the scan was successful
     * @return RedirectResponse
     */
    public function runScan(): RedirectResponse
    {
        $scan = $this->fraudScanningService->fraudScan();

        if (!$scan) {
            return redirect()->route('scans.index')->with('error', 'An error occurred while fetching customers from API.');
        }

        return redirect()->route('scans.latest');
    }


}
