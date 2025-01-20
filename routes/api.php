<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SensorReadingController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});

Route::prefix('devices')->controller(DeviceController::class)->group(function () {
    Route::post('/activate/{token}', 'activateDevice');
    Route::post('/deactivate/{token}', 'deactivateDevice');
    Route::get('/status/{token}', 'getDeviceByToken');
});

Route::prefix('readings')->controller((SensorReadingController::class))->group(function () {
    Route::post('/', 'createSensorReading');
});

Route::prefix('alerts')->controller(AlertController::class)->group(function () {
    Route::post('/', 'createAlert');
});

Route::middleware([IsUserAuth::class])->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'getUser');
    });

    Route::middleware([IsAdmin::class])->group(function () {
        Route::prefix('devices')->controller(DeviceController::class)->group(function () {
            Route::get('/', 'getDevices');
            Route::get('/{id}', 'getDevice');
            Route::post('/', 'createDevice');
            Route::put('/{id}', 'updateDevice');
            Route::delete('/{id}', 'deleteDevice');
        });

        Route::prefix('readings')->controller((SensorReadingController::class))->group(function () {
            Route::get('/', 'getSensorReadings');
            Route::get('/{id}', 'getSensorReading');
            Route::put('/{id}', 'updateSensorReading');
            Route::delete('/{id}', 'deleteSensorReading');
        });

        Route::prefix('alerts')->controller(AlertController::class)->group(function () {
            Route::get('/', 'getAlerts');
            Route::get('/{id}', 'getAlert');
            Route::put('/{id}', 'updateAlert');
            Route::delete('/{id}', 'deleteAlert');
        });
    });
});
