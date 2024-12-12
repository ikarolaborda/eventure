<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {
    Route::post('events', \App\Http\Controllers\Api\v1\Event\EventStoreController::class);
    Route::get('events', \App\Http\Controllers\Api\v1\Event\EventListController::class);
    Route::post('events/{id}/reserve', \App\Http\Controllers\Api\v1\Reservation\ReservationStoreController::class);

    Route::get('reservations', \App\Http\Controllers\Api\v1\Reservation\ReservationListController::class);
    Route::get('reservations/{id}', \App\Http\Controllers\Api\v1\Reservation\ReservationShowController::class);
    Route::put('reservations/{id}', \App\Http\Controllers\Api\v1\Reservation\ReservationUpdateController::class);
    Route::delete('reservations/{id}', \App\Http\Controllers\Api\v1\Reservation\ReservationDestroyController::class);
});


