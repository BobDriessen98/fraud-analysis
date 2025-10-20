<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScanResource;
use App\Models\Scan;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScanAPIController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $scans = Scan::with('customers.fraudReasons')->get();
        return ScanResource::collection($scans);
    }

    public function show(Scan $scan): ScanResource
    {
        $scan->load('customers.fraudReasons');
        return new ScanResource($scan);
    }
}
