<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $deviceName = $request->device_name ?? $request->header('User-Agent') ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                // अगर और safe fields चाहिए तो यहाँ जोड़ें
            ],
        ], 200);
    }

    // Logout - revoke current access token
    public function logout(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        // सिर्फ़ वर्तमान token हटाना
        $request->user()->currentAccessToken()->delete();

        // अगर आप सभी tokens revoke करना चाहें तो ये उपयोग करें:
        // $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // Update password (for authenticated user)
public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|string',
        'new_password'     => 'required|string|min:8|confirmed',
    ]);

    $user = $request->user();

    if (! Hash::check($request->current_password, $user->password)) {
        return redirect()->back()->with('error', '❌ वर्तमान पासवर्ड गलत है।');
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->back()->with('success', '✅ पासवर्ड सफलतापूर्वक बदल दिया गया है।');
}


    /* 
     Optional: Admin-reset-password endpoint (use only if admin is changing another user's password)
     public function adminResetPassword(Request $request) {
         $request->validate([
             'user_id' => 'required|exists:users,id',
             'new_password' => 'required|string|min:6|confirmed',
         ]);
         $user = User::find($request->user_id);
         $user->password = Hash::make($request->new_password);
         $user->save();
         return response()->json(['message' => 'Password changed for user.'], 200);
     }
    */
}
