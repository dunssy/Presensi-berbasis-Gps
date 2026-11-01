<?php

use App\Http\Controllers\Api\MapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('maps')->name('api.maps.')->group(function () {
    Route::get('/attendance-points', [MapController::class, 'getAttendancePoints'])->name('points');
    Route::get('/attendance-points/{attendancePoint}', [MapController::class, 'showPoint'])->name('points.show');
    Route::post('/validate-location', [MapController::class, 'validateLocation'])->name('validate');
});

