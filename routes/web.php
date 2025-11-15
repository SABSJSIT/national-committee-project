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

    // ✅ सिर्फ़ Super Admin Dashboard available रहेगा
    Route::get('/dashboard/super_admin', function () {
        return view('dashboards.super_admin.index');
    })->name('dashboard.super_admin');

    // Mahila Samiti Members Pages
    Route::get('/mahila-samiti-members', function () {
        return view('mahila_samiti_members.AddMahilaSamitiMembers');
    })->name('mahila-samiti-members');

    Route::get('/mahila-samiti-members/add', function () {
        return view('mahila_samiti_members.AddMember');
    })->name('mahila-samiti-members.add');
});
