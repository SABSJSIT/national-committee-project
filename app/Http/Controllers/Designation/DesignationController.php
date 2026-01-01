<?php

namespace App\Http\Controllers\Designation;

use App\Http\Controllers\Controller;
use App\Models\Designation\Designation;
use App\Models\DesignationType\DesignationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{
    /**
     * Display a listing of designations
     */
    public function index()
    {
        $designations = Designation::with('designationType')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $designations
        ]);
    }

    /**
     * Get designation types for dropdown
     */
    public function getDesignationTypes()
    {
        $designationTypes = DesignationType::orderBy('name', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $designationTypes
        ]);
    }

    /**
     * Store a newly created designation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation_type_id' => 'required|exists:designation_types,id',
            'designation_dept' => 'required|in:shree_sangh,mahila_samiti,yuva_sangh,all',
        ], [
            'name.required' => 'Designation का नाम आवश्यक है।',
            'name.max' => 'Designation का नाम 255 characters से अधिक नहीं हो सकता।',
            'designation_type_id.required' => 'Designation Type चुनना आवश्यक है।',
            'designation_type_id.exists' => 'चुना गया Designation Type मौजूद नहीं है।',
            'designation_dept.required' => 'Designation Department चुनना आवश्यक है।',
            'designation_dept.in' => 'कृपया एक valid Department चुनें।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate name within same designation_type
        $exists = Designation::where('name', $request->name)
            ->where('designation_type_id', $request->designation_type_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => [
                    'name' => ['इस Designation Type में यह Designation पहले से मौजूद है।']
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $designation = Designation::create([
                'name' => $request->name,
                'designation_type_id' => $request->designation_type_id,
                'designation_dept' => $request->designation_dept
            ]);

            // Load the relationship for response
            $designation->load('designationType');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Designation सफलतापूर्वक बनाया गया।',
                'data' => $designation
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Designation बनाने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified designation
     */
    public function show(string $id)
    {
        $designation = Designation::with('designationType')->find($id);

        if (!$designation) {
            return response()->json([
                'success' => false,
                'message' => 'Designation नहीं मिला।'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $designation
        ]);
    }

    /**
     * Update the specified designation
     */
    public function update(Request $request, string $id)
    {
        $designation = Designation::find($id);

        if (!$designation) {
            return response()->json([
                'success' => false,
                'message' => 'Designation नहीं मिला।'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation_type_id' => 'required|exists:designation_types,id',
            'designation_dept' => 'required|in:shree_sangh,mahila_samiti,yuva_sangh,all',
        ], [
            'name.required' => 'Designation का नाम आवश्यक है।',
            'name.max' => 'Designation का नाम 255 characters से अधिक नहीं हो सकता।',
            'designation_type_id.required' => 'Designation Type चुनना आवश्यक है।',
            'designation_type_id.exists' => 'चुना गया Designation Type मौजूद नहीं है।',
            'designation_dept.required' => 'Designation Department चुनना आवश्यक है।',
            'designation_dept.in' => 'कृपया एक valid Department चुनें।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate name within same designation_type (excluding current record)
        $exists = Designation::where('name', $request->name)
            ->where('designation_type_id', $request->designation_type_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => [
                    'name' => ['इस Designation Type में यह Designation पहले से मौजूद है।']
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $designation->update([
                'name' => $request->name,
                'designation_type_id' => $request->designation_type_id,
                'designation_dept' => $request->designation_dept
            ]);

            // Load the relationship for response
            $designation->load('designationType');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Designation सफलतापूर्वक अपडेट किया गया।',
                'data' => $designation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Designation अपडेट करने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified designation
     */
    public function destroy(string $id)
    {
        $designation = Designation::find($id);

        if (!$designation) {
            return response()->json([
                'success' => false,
                'message' => 'Designation नहीं मिला।'
            ], 404);
        }

        try {
            $designation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Designation सफलतापूर्वक हटाया गया।'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Designation हटाने में त्रुटि हुई।',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
