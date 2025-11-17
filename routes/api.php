<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\mahila_samiti_members\AddMahilaSamitiMembersController;

// Mahila Samiti Members Routes
Route::apiResource('mahila-samiti-members', AddMahilaSamitiMembersController::class);
Route::get('mahila-samiti-members-dropdown-data', [AddMahilaSamitiMembersController::class, 'getDropdownData']);
Route::get('mahila-samiti-members-existing-combinations', [AddMahilaSamitiMembersController::class, 'getExistingCombinations']);
Route::get('mahila-samiti-members-cities', [AddMahilaSamitiMembersController::class, 'getCities']);
Route::get('mahila-samiti-members-states', [AddMahilaSamitiMembersController::class, 'getStates']);

Route::post('/fetch-external-profile', [AddMahilaSamitiMembersController::class, 'fetchExternalProfile']);


