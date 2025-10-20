<?php

use App\Http\Controllers\ScanAPIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanViewController;

//Viewing routes
Route::get('/scans/', [ScanViewController::class, 'index'])->name('scans.index');
Route::get('/scans/latest', [ScanViewController::class, 'latestScan'])->name('scans.latest');
Route::get('/scans/{scan}', [ScanViewController::class, 'show'])->name('scans.show');

Route::post('/scans/run', [ScanViewController::class, 'runScan'])->name('scans.run');

//API routes (I'd normally put this in api.php but could not get that to work for some reason, so I put it here)
Route::get('api/scans/', [ScanAPIController::class, 'index']);
Route::get('api/scans/{scan}', [ScanAPIController::class, 'show']);
