<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Models\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Display a listing of sessions
     */
    public function index()
    {
        $sessions = Session::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Store a newly created session
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:shree/mahila,yuva',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Session का नाम आवश्यक है।',
            'name.max' => 'Session का नाम 255 characters से अधिक नहीं हो सकता।',
            'type.required' => 'Session का प्रकार चुनना आवश्यक है।',
            'type.in' => 'कृपया सही प्रकार चुनें (श्री/महिला या युवा)।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // If new session is being set as active, deactivate other sessions of same type
            if ($request->is_active) {
                Session::where('type', $request->type)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $session = Session::create([
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => $request->is_active ?? false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Session सफलतापूर्वक बनाया गया।',
                'data' => $session
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Session बनाने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified session
     */
    public function show(string $id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session नहीं मिला।'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    /**
     * Update the specified session
     */
    public function update(Request $request, string $id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session नहीं मिला।'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:shree/mahila,yuva',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Session का नाम आवश्यक है।',
            'name.max' => 'Session का नाम 255 characters से अधिक नहीं हो सकता।',
            'type.required' => 'Session का प्रकार चुनना आवश्यक है।',
            'type.in' => 'कृपया सही प्रकार चुनें (श्री/महिला या युवा)।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // If this session is being activated, deactivate other sessions of same type
            if ($request->is_active && !$session->is_active) {
                Session::where('type', $request->type)
                    ->where('id', '!=', $id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            // If type is changed and session is active, deactivate other sessions of new type
            if ($request->is_active && $session->type !== $request->type) {
                Session::where('type', $request->type)
                    ->where('id', '!=', $id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $session->update([
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => $request->is_active ?? false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Session सफलतापूर्वक अपडेट किया गया।',
                'data' => $session
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Session अपडेट करने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified session
     */
    public function destroy(string $id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session नहीं मिला।'
            ], 404);
        }

        try {
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session सफलतापूर्वक हटाया गया।'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Session हटाने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status for a session
     */
    public function toggleActive(string $id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session नहीं मिला।'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // If activating this session, deactivate others of same type
            if (!$session->is_active) {
                Session::where('type', $session->type)
                    ->where('id', '!=', $id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $session->update(['is_active' => !$session->is_active]);

            DB::commit();

            $statusText = $session->is_active ? 'सक्रिय' : 'निष्क्रिय';
            return response()->json([
                'success' => true,
                'message' => "Session $statusText किया गया।",
                'data' => $session
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Status बदलने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
