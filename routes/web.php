<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* --------------------------------------------
|  Change Password (only for authenticated)
|---------------------------------------------*/
Route::middleware('auth')->group(function () {
    Route::view('/change-password', 'change_password_dashboards.change-password')
        ->name('change-password');

    Route::post('/change-password', [AuthController::class, 'updatePassword'])
        ->name('password.update');
});

/* --------------------------------------------
|  Login Page (public)
|---------------------------------------------*/
Route::get('/', function () {
    return view('login');
})->name('login');

/* --------------------------------------------
|  Login Submit
|---------------------------------------------*/
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return back()->with('error', 'Invalid credentials');
    }

    auth()->login($user);
    session(['user' => auth()->user()]);
    $request->session()->regenerate();

    Log::info('LOGIN: user id=' . auth()->id());

    // ✅ हमेशा Super Admin dashboard पर redirect
    return redirect()->route('dashboard.super_admin');
})->name('login.submit');

/* --------------------------------------------
|  Logout
|---------------------------------------------*/
// Logout
Route::post('/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');


/* --------------------------------------------
|  Authenticated Area
|---------------------------------------------*/
Route::middleware(['web', 'checkSession'])->group(function () {

    // ✅ Dashboard - सभी authenticated users के लिए
    Route::get('/dashboard/super_admin', function () {
        return view('dashboards.super_admin.index');
    })->name('dashboard.super_admin');

    /* ----------------------------------------
    |  SUPER ADMIN ONLY ROUTES
    |  (super_admin को हमेशा access है via middleware)
    |-----------------------------------------*/
    Route::middleware(['matchRole:super_admin'])->group(function () {
        // Session Management - Only Super Admin
        Route::get('/session-management', function () {
            return view('session.Session');
        })->name('session.management');

        // Designation Type Management - Only Super Admin
        Route::get('/designation-type-management', function () {
            return view('DesignationType.DesignationType');
        })->name('designation-type.management');
    });

    /* ----------------------------------------
    |  MAHILA SAMITI ROUTES
    |  (super_admin + mahila_samiti users)
    |-----------------------------------------*/
    Route::middleware(['matchRole:mahila_samiti'])->group(function () {
        // Mahila Samiti Members Pages
        Route::get('/mahila-samiti-members', function () {
            return view('mahila_samiti_members.AddMahilaSamitiMembers');
        })->name('mahila-samiti-members');

        Route::get('/mahila-samiti-members/add', function () {
            return view('mahila_samiti_members.AddMember');
        })->name('mahila-samiti-members.add');

        // FPDF Export Route
        Route::get('/mahila-samiti-members/export-fpdf', [\App\Http\Controllers\mahila_samiti_members\MahilaSamitiMembersExportController::class, 'exportFPDF'])->name('mahila-samiti-members.export-fpdf');
    });

    /* ----------------------------------------
    |  SHRAMNOPASAK ROUTES (Example - add your routes)
    |  (super_admin + shramnopasak users)
    |-----------------------------------------*/
    // Route::middleware(['matchRole:shramnopasak'])->group(function () {
    //     Route::get('/shramnopasak', function () {
    //         return view('shramnopasak.index');
    //     })->name('shramnopasak');
    // });

    /* ----------------------------------------
    |  SAHITYA ROUTES (Example - add your routes)
    |  (super_admin + sahitya users)
    |-----------------------------------------*/
    // Route::middleware(['matchRole:sahitya'])->group(function () {
    //     Route::get('/sahitya', function () {
    //         return view('sahitya.index');
    //     })->name('sahitya');
    // });

    /* ----------------------------------------
    |  DISPATCH ROUTES (Example - add your routes)
    |  (super_admin + dispatch users)
    |-----------------------------------------*/
    // Route::middleware(['matchRole:dispatch'])->group(function () {
    //     Route::get('/dispatch', function () {
    //         return view('dispatch.index');
    //     })->name('dispatch');
    // });

    /* ----------------------------------------
    |  YUVA SANGH ROUTES (Example - add your routes)
    |  (super_admin + yuva_sangh users)
    |-----------------------------------------*/
    // Route::middleware(['matchRole:yuva_sangh'])->group(function () {
    //     Route::get('/yuva-sangh', function () {
    //         return view('yuva_sangh.index');
    //     })->name('yuva-sangh');
    // });
});
