<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

Route::get('/test', function () {
    return response()->json(['ok' => true]);
});

Route::post('/device-multi', [DeviceController::class, 'getMultipleDevices']);