<?php

namespace App\Http\Controllers\mahila_samiti_members;

use App\Http\Controllers\Controller;
use App\Models\mahila_samiti_members\AddMahilaSamitiMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AddMahilaSamitiMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $members = AddMahilaSamitiMembers::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $members,
                'message' => 'Data retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make(
                $request->all(),
                AddMahilaSamitiMembers::validationRules(),
                AddMahilaSamitiMembers::validationMessages()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            

            // Specific validation based on type and session/anchal combination
            $sessionValidationResult = $this->validateSessionAnchalLimits($request);
            if (!$sessionValidationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $sessionValidationResult['message']
                ], 422);
            }

            $data = $request->only([
                'session', 'anchal_name', 'anchal_code', 'type', 'designation',
                'mid', 'name', 'name_hindi', 'husband_name', 'husband_name_hindi', 'father_name', 'father_name_hindi',
                'address', 'address_hindi', 'city', 'state', 'pincode', 'mobile_number', 'wtp_number',
                'ex_post', 'remarks'
            ]);

            // Handle photo upload if present
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                
                // Validate image size (200KB)
                if ($photo->getSize() > 200 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 200KB'
                    ], 422);
                }
                
                // Create session-based folder path
                $session = $data['session'];
                $folderPath = 'public/mahila_samiti/' . $session;
                
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs($folderPath, $photoName);
                $data['photo'] = $session . '/' . $photoName;
            }

            $member = AddMahilaSamitiMembers::create($data);

            return response()->json([
                'success' => true,
                'data' => $member,
                'message' => 'Member added successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $member = AddMahilaSamitiMembers::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $member,
                'message' => 'Data retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $member = AddMahilaSamitiMembers::findOrFail($id);

            // Validate the request
            $validator = Validator::make(
                $request->all(),
                AddMahilaSamitiMembers::validationRules($id),
                AddMahilaSamitiMembers::validationMessages()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            // Check if entry already exists for this session and mid (excluding current record)
            if (AddMahilaSamitiMembers::sessionEntryExists($request->input('session'), $request->input('mid'), $id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entry for this session and MID already exists'
                ], 400);
            }

            // Check for duplicate type-designation combination for the same session (excluding current record)
            if (AddMahilaSamitiMembers::typeDesignationExists($request->input('session'), $request->input('type'), $request->input('designation'), $id)) {
                $sessionDisplay = $request->input('session');
                $typeDisplay = ucfirst(str_replace('-', ' ', $request->input('type')));
                $designationDisplay = $request->input('designation');
                
                return response()->json([
                    'success' => false,
                    'message' => "You have already added a {$typeDisplay} {$designationDisplay} for session {$sessionDisplay}. Each session can have only one member per type-designation combination."
                ], 422);
            }

            $data = $request->only([
                'session', 'anchal_name', 'anchal_code', 'type', 'designation',
                'mid', 'name', 'name_hindi', 'husband_name', 'husband_name_hindi', 'father_name', 'father_name_hindi',
                'address', 'address_hindi', 'city', 'state', 'pincode', 'mobile_number', 'wtp_number',
                'ex_post', 'remarks'
            ]);

            // Handle photo upload if present
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($member->photo && Storage::exists('public/mahila_samiti/' . $member->photo)) {
                    Storage::delete('public/mahila_samiti/' . $member->photo);
                }

                $photo = $request->file('photo');
                
                // Validate image size (200KB)
                if ($photo->getSize() > 200 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 200KB'
                    ], 422);
                }
                
                // Create session-based folder path
                $session = $data['session'] ?? $member->session;
                $folderPath = 'public/mahila_samiti/' . $session;
                
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs($folderPath, $photoName);
                $data['photo'] = $session . '/' . $photoName;
            }

            $member->update($data);

            return response()->json([
                'success' => true,
                'data' => $member,
                'message' => 'Member updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $member = AddMahilaSamitiMembers::findOrFail($id);

            // Delete photo if exists (session-based path)
            if ($member->photo && Storage::exists('public/mahila_samiti/' . $member->photo)) {
                Storage::delete('public/mahila_samiti/' . $member->photo);
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Member deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate session and anchal specific entry limits
     */
    private function validateSessionAnchalLimits($request, $id = null)
    {
        $session = $request->input('session');
        $anchalName = $request->input('anchal_name');
        $type = $request->input('type');
        $designation = $request->input('designation');

        // For PST, check across all anchals for the session
        $query = AddMahilaSamitiMembers::where('session', $session)
                    ->where('type', $type);
        
        // For other types, include anchal in the check
        if ($type !== 'pst') {
            $query->where('anchal_name', $anchalName);
        }
        
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $existingCount = $query->count();
        $existingDesignations = $query->pluck('designation')->toArray();

        switch ($type) {
            case 'pst':
                // PST: Only one of each designation per session (across all anchals)
                if (in_array($designation, $existingDesignations)) {
                    return [
                        'valid' => false,
                        'message' => "A {$designation} for {$type} already exists in session {$session}. Each session can have only one {$designation} across all anchals."
                    ];
                }
                break;

            case 'vp-sec':
                // VP-SEC: Only one Vice-President and one Secretary per session per anchal
                if (in_array($designation, $existingDesignations)) {
                    return [
                        'valid' => false,
                        'message' => "A {$designation} for VP-SEC already exists for {$anchalName} in session {$session}. Each anchal can have only one {$designation} per session."
                    ];
                }
                break;

            case 'sanyojika':
                // Sanyojika: Maximum 9 entries per session per anchal
                if ($existingCount >= 9) {
                    return [
                        'valid' => false,
                        'message' => "Maximum 9 Sanyojika members allowed per anchal per session. {$anchalName} already has {$existingCount} Sanyojika members for session {$session}."
                    ];
                }
                // Check if this specific designation already exists
                if (in_array($designation, $existingDesignations)) {
                    return [
                        'valid' => false,
                        'message' => "{$designation} already exists for {$anchalName} in session {$session}. Each option can have only one entry."
                    ];
                }
                break;

            case 'ksm_members':
                // KSM Members: Maximum 12 entries per session per anchal
                if ($existingCount >= 12) {
                    return [
                        'valid' => false,
                        'message' => "Maximum 12 KSM members allowed per anchal per session. {$anchalName} already has {$existingCount} KSM members for session {$session}."
                    ];
                }
                break;
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Get dropdown data for form
     */
    public function getDropdownData()
    {
        try {
            // Generate session options (academic year format)
            $sessions = [];
            $startYear = 2020;
            $currentYear = date('Y');
            
            // Generate sessions like 2020-2021, 2021-2022, etc.
            for ($year = $startYear; $year <= $currentYear + 5; $year++) {
                $sessions[] = $year . '-' . ($year + 1);
            }

            // Fetch anchals from database
            $anchals = [];
            if (DB::getSchemaBuilder()->hasTable('anchal')) {
                $anchals = DB::table('anchal')
                    ->select('anchal_id as id', 'name')
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->get()
                    ->toArray();
            }

            // Fetch cities from database
            $cities = [];
            if (DB::getSchemaBuilder()->hasTable('cities')) {
                $cities = DB::table('cities')
                    ->select('city_id as id', 'city_name as name', 'state_id', 'anchal_id')
                    ->orderBy('city_name')
                    ->get()
                    ->toArray();
            }

            // Fetch states from database
            $states = [];
            if (DB::getSchemaBuilder()->hasTable('states')) {
                $states = DB::table('states')
                    ->select('state_id as id', 'state_name as name', 'state_code')
                    ->orderBy('state_name')
                    ->get()
                    ->toArray();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessions,
                    'anchals' => $anchals,
                    'cities' => $cities,
                    'states' => $states,
                    'types' => [
                        'pst' => 'PST',
                        'vp-sec' => 'VP-SEC',
                        'sanyojika' => 'Sanyojika',
                        'ksm_members' => 'KSM Members'
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dropdown data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all cities for dropdown
     */
    public function getCities()
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('cities')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cities table not found'
                ], 404);
            }

            $cities = DB::table('cities')
                ->select('city_id as code', 'city_name as name', 'state_id', 'anchal_id', 'dist_id', 'pincode', 'stdcode')
                ->orderBy('city_name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'cities' => $cities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all states for dropdown
     */
    public function getStates()
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('states')) {
                return response()->json([
                    'success' => false,
                    'message' => 'States table not found'
                ], 404);
            }

            $states = DB::table('states')
                ->select('state_id as code', 'state_name as name', 'state_code', 'zone_id')
                ->orderBy('state_name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'states' => $states
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching states: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing type-designation combinations for a session, anchal, and type
     */
    public function getExistingCombinations(Request $request)
    {
        try {
            $session = $request->input('session');
            $anchalName = $request->input('anchal_name');
            $type = $request->input('type');
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is required'
                ], 400);
            }

            $query = AddMahilaSamitiMembers::where('session', $session);
            
            // Filter by anchal if provided
            if ($anchalName) {
                $query->where('anchal_name', $anchalName);
            }
            
            // Filter by type if provided
            if ($type) {
                $query->where('type', $type);
            }

            // Get existing designations for the filtered criteria
            $existingDesignations = $query->pluck('designation')->toArray();

            return response()->json([
                'success' => true,
                'combinations' => $existingDesignations,
                'count' => count($existingDesignations)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching existing combinations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch external profile data (proxy to avoid CORS issues)
     */
    public function fetchExternalProfile(Request $request)
    {
        $request->validate([
            'mid' => 'required|string|min:3'
        ]);

        try {
            $mid = $request->mid;
            
            // Make API call to external service
            $client = new \GuzzleHttp\Client();
            
            // Try different request formats since the API is returning 401
            $response = $client->post('https://apiv1.sadhumargi.com/api/fetch-profiles', [
                'form_params' => [
                    'mid' => $mid
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Laravel/Application',
                    'X-Requested-With' => 'XMLHttpRequest'
                ],
                'timeout' => 30,
                'verify' => false // Disable SSL verification if needed
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            return response()->json([
                'success' => true,
                'profiles' => $data['data'] ?? []
            ]);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle 4xx errors (like 401, 403, etc.)
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            
            Log::error('External API Client Error', [
                'status_code' => $statusCode,
                'response' => $responseBody,
                'mid' => $mid
            ]);
            
            return response()->json([
                'success' => false,
                'message' => "External API Error ({$statusCode}): API authentication or authorization failed",
                'details' => 'The external profile service requires proper authentication credentials'
            ], 422);

        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle 5xx errors
            $statusCode = $e->getResponse()->getStatusCode();
            
            return response()->json([
                'success' => false,
                'message' => "External API Server Error ({$statusCode}): Service temporarily unavailable"
            ], 422);

        } catch (\Exception $e) {
            Log::error('External API Call Failed', [
                'error' => $e->getMessage(),
                'mid' => $mid
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to external profile service: ' . $e->getMessage()
            ], 500);
        }
    }
}