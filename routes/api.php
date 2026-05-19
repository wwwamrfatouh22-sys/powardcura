<?php

use App\Http\Controllers\Api\HospitalApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin-api,doctor-api,staff-api', 'role:admin,doctor,staff'])->group(function () {
    Route::get('/doctors', [HospitalApiController::class, 'doctors']);
    Route::get('/departments', [HospitalApiController::class, 'departments']);
    Route::get('/appointments', [HospitalApiController::class, 'appointments']);
    Route::get('/doctors/{doctor}/appointments', [HospitalApiController::class, 'doctorAppointments']);
});

Route::middleware(['auth:admin-api,staff-api', 'role:admin,staff'])->group(function () {
    Route::post('/doctors', [HospitalApiController::class, 'storeDoctor']);
    Route::put('/doctors/{doctor}', [HospitalApiController::class, 'updateDoctor']);
    Route::delete('/doctors/{doctor}', [HospitalApiController::class, 'deleteDoctor']);

    Route::post('/departments', [HospitalApiController::class, 'storeDepartment']);
    Route::put('/departments/{department}', [HospitalApiController::class, 'updateDepartment']);
    Route::delete('/departments/{department}', [HospitalApiController::class, 'deleteDepartment']);

    Route::post('/appointments', [HospitalApiController::class, 'storeAppointment']);
    Route::put('/appointments/{appointment}', [HospitalApiController::class, 'updateAppointment']);
    Route::delete('/appointments/{appointment}', [HospitalApiController::class, 'deleteAppointment']);
});
