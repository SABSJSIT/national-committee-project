<?php

namespace App\Http\Controllers\DesignationType;

use App\Http\Controllers\Controller;
use App\Models\DesignationType\DesignationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DesignationTypeController extends Controller
{
    /**
     * Display a listing of designation types
     */
    public function index()
    {
        $designationTypes = DesignationType::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $designationTypes
        ]);
    }

    /**
     * Store a newly created designation type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:designation_types,name',
            'type' => 'required|in:shree_sangh,mahila_samiti,yuva_sangh,for_all',
        ], [
            'name.required' => 'पदनाम का नाम आवश्यक है।',
            'name.max' => 'पदनाम का नाम 255 characters से अधिक नहीं हो सकता।',
            'name.unique' => 'यह पदनाम पहले से मौजूद है।',
            'type.required' => 'प्रकार चुनना आवश्यक है।',
            'type.in' => 'कृपया सही प्रकार चुनें।',
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

            $designationType = DesignationType::create([
                'name' => $request->name,
                'type' => $request->type
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'पदनाम प्रकार सफलतापूर्वक बनाया गया।',
                'data' => $designationType
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'पदनाम प्रकार बनाने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified designation type
     */
    public function show(string $id)
    {
        $designationType = DesignationType::find($id);

        if (!$designationType) {
            return response()->json([
                'success' => false,
                'message' => 'पदनाम प्रकार नहीं मिला।'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $designationType
        ]);
    }

    /**
     * Update the specified designation type
     */
    public function update(Request $request, string $id)
    {
        $designationType = DesignationType::find($id);

        if (!$designationType) {
            return response()->json([
                'success' => false,
                'message' => 'पदनाम प्रकार नहीं मिला।'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:designation_types,name,' . $id,
            'type' => 'required|in:shree_sangh,mahila_samiti,yuva_sangh,for_all',
        ], [
            'name.required' => 'पदनाम का नाम आवश्यक है।',
            'name.max' => 'पदनाम का नाम 255 characters से अधिक नहीं हो सकता।',
            'name.unique' => 'यह पदनाम पहले से मौजूद है।',
            'type.required' => 'प्रकार चुनना आवश्यक है।',
            'type.in' => 'कृपया सही प्रकार चुनें।',
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

            $designationType->update([
                'name' => $request->name,
                'type' => $request->type
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'पदनाम प्रकार सफलतापूर्वक अपडेट किया गया।',
                'data' => $designationType
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'पदनाम प्रकार अपडेट करने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified designation type
     */
    public function destroy(string $id)
    {
        $designationType = DesignationType::find($id);

        if (!$designationType) {
            return response()->json([
                'success' => false,
                'message' => 'पदनाम प्रकार नहीं मिला।'
            ], 404);
        }

        try {
            $designationType->delete();

            return response()->json([
                'success' => true,
                'message' => 'पदनाम प्रकार सफलतापूर्वक हटाया गया।'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'पदनाम प्रकार हटाने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
