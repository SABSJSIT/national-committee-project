<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\mahila_samiti_members\AddMahilaSamitiMembersController;
use App\Http\Controllers\Session\SessionController;
use App\Http\Controllers\DesignationType\DesignationTypeController;
use App\Http\Controllers\Designation\DesignationController;

// Designation Type Routes
Route::apiResource('designation-types', DesignationTypeController::class);

// Designation Routes
Route::get('designations/get-designation-types', [DesignationController::class, 'getDesignationTypes']);
Route::apiResource('designations', DesignationController::class);

// Mahila Samiti Members Routes
// Check duplicate by session + MID (must be BEFORE apiResource to avoid being matched as {id})
Route::get('mahila-samiti-members/check-duplicate', [AddMahilaSamitiMembersController::class, 'checkDuplicate']);

Route::apiResource('mahila-samiti-members', AddMahilaSamitiMembersController::class);
Route::get('mahila-samiti-members-dropdown-data', [AddMahilaSamitiMembersController::class, 'getDropdownData']);
Route::get('mahila-samiti-members-dropdown-data-all', [AddMahilaSamitiMembersController::class, 'getDropdownDataAll']);
Route::get('mahila-samiti-members-filter-options', [AddMahilaSamitiMembersController::class, 'getFilterOptions']);
Route::get('mahila-samiti-members-existing-combinations', [AddMahilaSamitiMembersController::class, 'getExistingCombinations']);
Route::get('mahila-samiti-members-cities', [AddMahilaSamitiMembersController::class, 'getCities']);
Route::get('mahila-samiti-members-cities-by-anchal', [AddMahilaSamitiMembersController::class, 'getCitiesByAnchal']);
Route::get('mahila-samiti-members-states', [AddMahilaSamitiMembersController::class, 'getStates']);
Route::get('mahila-samiti-members-designations-by-type', [AddMahilaSamitiMembersController::class, 'getDesignationsByType']);

Route::post('/fetch-external-profile', [AddMahilaSamitiMembersController::class, 'fetchExternalProfile']);

// Session Routes
Route::apiResource('sessions', SessionController::class);
Route::post('sessions/{id}/toggle', [SessionController::class, 'toggleActive']);

