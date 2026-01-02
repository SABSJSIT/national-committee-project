<?php

namespace App\Http\Controllers\mahila_samiti_members;

use App\Http\Controllers\Controller;
use App\Models\mahila_samiti_members\AddMahilaSamitiMembers;
use App\Models\Session\Session;
use App\Models\DesignationType\DesignationType;
use App\Models\Designation\Designation;
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
    public function index(Request $request)
    {
        try {
            $query = AddMahilaSamitiMembers::query();
            
            // Filter by session if provided
            if ($request->has('session') && $request->session) {
                $query->where('session', $request->session);
            }
            
            $members = $query->orderBy('created_at', 'desc')->get();
            
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
            // Log request files to debug photo upload
            Log::info('Request files:', $request->allFiles());
            Log::info('Has file photo_file:', ['hasFile' => $request->hasFile('photo_file')]);
            Log::info('Has photo field:', ['photo' => $request->input('photo')]);

            // Check if photo is uploaded as file or base64
            if ($request->hasFile('photo_file')) {
                // Handle file upload
                $photo = $request->file('photo_file');

                // Validate image size (200KB)
                if ($photo->getSize() > 200 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 200KB'
                    ], 422);
                }

                // Create session-based folder path
                $session = $data['session'] ?? 'default';
                $folderPath = 'public/mahila_samiti/' . $session;

                $photoName = time() . '_' . preg_replace('/\s+/', '_', $photo->getClientOriginalName());

                Log::info('Attempting to store photo at:', ['path' => $folderPath . '/' . $photoName]);

                $photo->storeAs($folderPath, $photoName);
                $data['photo'] = $session . '/' . $photoName;
            } elseif ($request->input('photo') && strpos($request->input('photo'), 'data:image') === 0) {
                // Handle base64 image
                $base64Image = $request->input('photo');
                
                // Extract image data
                $imageData = explode(',', $base64Image)[1];
                $decodedImage = base64_decode($imageData);
                
                // Validate image size (200KB)
                if (strlen($decodedImage) > 200 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 200KB'
                    ], 422);
                }
                
                // Get image extension from base64 header
                $mimeType = explode(';', explode(':', $base64Image)[1])[0];
                $extension = explode('/', $mimeType)[1];
                if ($extension === 'jpeg') $extension = 'jpg';
                
                // Create session-based folder path
                $session = $data['session'] ?? 'default';
                $folderPath = 'storage/app/public/mahila_samiti/' . $session;
                
                // Create directory if it doesn't exist
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }
                
                $photoName = time() . '_photo.' . $extension;
                $fullPath = $folderPath . '/' . $photoName;
                
                Log::info('Attempting to store base64 photo at:', ['path' => $fullPath]);
                
                // Save the image
                if (file_put_contents($fullPath, $decodedImage)) {
                    $data['photo'] = $session . '/' . $photoName;
                    Log::info('Base64 photo saved successfully:', ['path' => $data['photo']]);
                } else {
                    Log::error('Failed to save base64 photo');
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save photo'
                    ], 422);
                }
            } else {
                Log::info('No photo uploaded.');
                $data['photo'] = null; // Set photo to null if not uploaded
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
            // BUT: Only check if session or mid has CHANGED from the original
            $session = $request->input('session');
            $mid = $request->input('mid');
            
            // Skip duplicate MID check - same MID can have multiple entries in same session
            // if they have different types or designations
            // The real restriction is on type+designation combination (checked below)

            // Check for duplicate type-designation combination for the same session (excluding current record)
            // Skip this check for KSM members - they can have multiple entries with same designation (limited by count only)
            $type = $request->input('type');
            $isKSMType = (stripos($type, 'ksm') !== false || stripos($type, 'केएसएम') !== false);
            
            if (!$isKSMType && AddMahilaSamitiMembers::typeDesignationExists($request->input('session'), $type, $request->input('designation'), $id)) {
                $sessionDisplay = $request->input('session');
                $typeDisplay = ucfirst(str_replace('-', ' ', $type));
                $designationDisplay = $request->input('designation');
                
                return response()->json([
                    'success' => false,
                    'message' => "You have already added a {$typeDisplay} {$designationDisplay} for session {$sessionDisplay}. Each session can have only one member per type-designation combination."
                ], 422);
            }

            // Specific validation based on type and session/anchal combination (including संयोजक/संयोजिका check)
            $sessionValidationResult = $this->validateSessionAnchalLimits($request, $id);
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
            if ($request->hasFile('photo_file')) {
                // Delete old photo if exists
                if ($member->photo && Storage::exists('public/mahila_samiti/' . $member->photo)) {
                    Storage::delete('public/mahila_samiti/' . $member->photo);
                }

                $photo = $request->file('photo_file');

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

                $photoName = time() . '_' . preg_replace('/\s+/', '_', $photo->getClientOriginalName());
                $photo->storeAs($folderPath, $photoName);
                $data['photo'] = $session . '/' . $photoName;
            } elseif ($request->input('photo') && strpos($request->input('photo'), 'data:image') === 0) {
                // Delete old photo if exists
                if ($member->photo && Storage::exists('public/mahila_samiti/' . $member->photo)) {
                    Storage::delete('public/mahila_samiti/' . $member->photo);
                }

                // Handle base64 image
                $base64Image = $request->input('photo');
                
                // Extract image data
                $imageData = explode(',', $base64Image)[1];
                $decodedImage = base64_decode($imageData);
                
                // Validate image size (200KB)
                if (strlen($decodedImage) > 200 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 200KB'
                    ], 422);
                }
                
                // Get image extension from base64 header
                $mimeType = explode(';', explode(':', $base64Image)[1])[0];
                $extension = explode('/', $mimeType)[1];
                if ($extension === 'jpeg') $extension = 'jpg';
                
                // Create session-based folder path
                $session = $data['session'] ?? $member->session;
                $folderPath = 'storage/app/public/mahila_samiti/' . $session;
                
                // Create directory if it doesn't exist
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }
                
                $photoName = time() . '_photo.' . $extension;
                $fullPath = $folderPath . '/' . $photoName;
                
                // Save the image
                if (file_put_contents($fullPath, $decodedImage)) {
                    $data['photo'] = $session . '/' . $photoName;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save photo'
                    ], 422);
                }
            } else {
                // Keep existing photo if no new photo is provided
                unset($data['photo']);
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

        // Check if designation contains "संयोजक" or "संयोजिका" (Hindi or English) - special validation needed
        $isSanyojak = (stripos($designation, 'संयोजक') !== false || stripos($designation, 'संयोजिका') !== false || stripos($designation, 'Sanyojak') !== false || stripos($designation, 'Sanyojika') !== false);
        
        if ($isSanyojak) {
            // Check if any संयोजक/संयोजिका already exists for this type and session
            if (AddMahilaSamitiMembers::sanyojikaExistsForType($session, $type, $id)) {
                return [
                    'valid' => false,
                    'message' => "प्रत्येक प्रवर्ती ({$type}) में केवल एक संयोजक/संयोजिका हो सकता है। सत्र {$session} में इस प्रवर्ती में पहले से ही एक संयोजक/संयोजिका मौजूद है।"
                ];
            }
        }

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
                // Main posts like President, Secretary, Treasurer are unique per session
                if (in_array($designation, $existingDesignations)) {
                    // Normalize designation for better message
                    $normalizedDesig = trim(strtolower($designation));
                    $isMainPost = in_array($normalizedDesig, ['president', 'secretary', 'treasurer', 'co treasurer']);
                    
                    if ($isMainPost) {
                        return [
                            'valid' => false,
                            'message' => "केवल एक {$designation} प्रति सत्र की अनुमति है। सत्र {$session} में पहले से ही एक {$designation} मौजूद है।"
                        ];
                    } else {
                        return [
                            'valid' => false,
                            'message' => "A {$designation} for {$type} already exists in session {$session}. Each session can have only one {$designation} across all anchals."
                        ];
                    }
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
                // Sanyojika: Maximum 13 entries per session per anchal (updated request)
                if ($existingCount >= 13) {
                    return [
                        'valid' => false,
                        'message' => "Maximum 13 Sanyojika members allowed per anchal per session. {$anchalName} already has {$existingCount} Sanyojika members for session {$session}."
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
                    'message' => "प्रति अंचल अधिकतम 12 KSM सदस्यों की अनुमति है। {$anchalName} में सत्र {$session} के लिए पहले से ही {$existingCount} KSM सदस्य हैं। (Maximum 12 KSM members allowed per anchal per session. {$anchalName} already has {$existingCount} KSM members for session {$session}.)"
                ];
            }
            break;
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Get dropdown data for form (Optimized for performance)
     * Sessions and Designation Types are fetched fresh for real-time updates
     * Static data (anchals, states) are cached for performance
     */
    public function getDropdownData()
    {
        try {
            // Get ONLY ACTIVE sessions from sessions table for 'shree/mahila' type
            // NO CACHING - to reflect changes immediately
            // Only fetch active sessions for dropdown
            $sessions = Session::where('type', 'shree/mahila')
                ->where('is_active', true)
                ->orderBy('name', 'desc')
                ->get(['name', 'is_active'])
                ->toArray();

            // Get designation types for mahila_samiti (for the Type dropdown)
            // NO CACHING - to reflect changes immediately
            $designationTypes = DesignationType::select('id', 'name', 'type')
                ->whereIn('type', ['mahila_samiti', 'for_all'])
                ->orderBy('name')
                ->get()
                ->toArray();

            // Cache key for static dropdown data
            $staticCacheKey = 'mahila_samiti_static_dropdown_data';
            
            // Try to get static data from cache first
            $cachedStaticData = cache($staticCacheKey);
            
            if ($cachedStaticData) {
                $anchals = $cachedStaticData['anchals'];
                $states = $cachedStaticData['states'];
            } else {
                // Fetch anchals from database (only 12 records - static data)
                $anchals = DB::table('anchal')
                    ->select('anchal_id as id', 'name')
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->get()
                    ->toArray();

                // Fetch states from database (only 36 records - static data)
                $states = DB::table('states')
                    ->select('state_id as id', 'state_name as name', 'state_code')
                    ->orderBy('state_name')
                    ->get()
                    ->toArray();

                // Cache static data for 24 hours
                cache([$staticCacheKey => ['anchals' => $anchals, 'states' => $states]], now()->addDay());
            }

            // NOTE: Cities are NOT loaded here (1700+ records)
            // They are lazy-loaded via getCitiesByAnchal() when user selects anchal

            $dropdownData = [
                'sessions' => $sessions,
                'anchals' => $anchals,
                'cities' => [], // Empty - lazy loaded
                'states' => $states,
                'designationTypes' => $designationTypes
            ];

            return response()->json([
                'success' => true,
                'data' => $dropdownData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dropdown data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dropdown data for list page (with all sessions including inactive ones)
     */
    public function getDropdownDataAll()
    {
        try {
            // Get ALL sessions from sessions table for 'shree/mahila' type (for list page)
            // NO CACHING - to reflect changes immediately
            // Include is_active flag so frontend can auto-select the active one
            $sessions = Session::where('type', 'shree/mahila')
                ->orderBy('name', 'desc')
                ->get(['name', 'is_active'])
                ->toArray();

            // Get designation types for mahila_samiti (for the Type dropdown)
            // NO CACHING - to reflect changes immediately
            $designationTypes = DesignationType::select('id', 'name', 'type')
                ->whereIn('type', ['mahila_samiti', 'for_all'])
                ->orderBy('name')
                ->get()
                ->toArray();

            // Cache key for static dropdown data
            $staticCacheKey = 'mahila_samiti_static_dropdown_data';
            
            // Try to get static data from cache first
            $cachedStaticData = cache($staticCacheKey);
            
            if ($cachedStaticData) {
                $anchals = $cachedStaticData['anchals'];
                $states = $cachedStaticData['states'];
            } else {
                // Fetch anchals from database (only 12 records - static data)
                $anchals = DB::table('anchal')
                    ->select('anchal_id as id', 'name')
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->get()
                    ->toArray();

                // Fetch states from database (only 36 records - static data)
                $states = DB::table('states')
                    ->select('state_id as id', 'state_name as name', 'state_code')
                    ->orderBy('state_name')
                    ->get()
                    ->toArray();

                // Cache static data for 24 hours
                cache([$staticCacheKey => ['anchals' => $anchals, 'states' => $states]], now()->addHours(24));
            }

            // NOTE: Cities are NOT loaded here (1700+ records)
            // They are lazy-loaded via getCitiesByAnchal() when user selects anchal

            $dropdownData = [
                'sessions' => $sessions,
                'anchals' => $anchals,
                'cities' => [], // Empty - lazy loaded
                'states' => $states,
                'designationTypes' => $designationTypes
            ];

            return response()->json([
                'success' => true,
                'data' => $dropdownData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dropdown data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter options (anchals and states) from database for dropdown filters
     * Option 1: From existing members data (current approach)
     * Option 2: From static tables (uncomment the static section below)
     */
    public function getFilterOptions()
    {
        try {
            // Cache key for filter options
            $cacheKey = 'mahila_samiti_filter_options';
            
            // Try to get data from cache first
            $cachedData = cache($cacheKey);
            if ($cachedData) {
                return response()->json([
                    'success' => true,
                    'data' => $cachedData
                ], 200);
            }

            // OPTION 1: Get from existing members data (shows only anchals/states with existing members)
            /*
            $anchals = AddMahilaSamitiMembers::select('anchal_name')
                ->distinct()
                ->whereNotNull('anchal_name')
                ->where('anchal_name', '!=', '')
                ->orderBy('anchal_name')
                ->pluck('anchal_name')
                ->toArray();

            $states = AddMahilaSamitiMembers::select('state')
                ->distinct()
                ->whereNotNull('state')
                ->where('state', '!=', '')
                ->orderBy('state')
                ->pluck('state')
                ->toArray();
            */

            // OPTION 2: Get from static tables (shows ALL possible anchals/states)
            $anchals = DB::table('anchal')
                ->select('name')
                ->orderBy('display_order')
                ->orderBy('name')
                ->pluck('name')
                ->toArray();

            $states = DB::table('states')
                ->select('state_name as name')
                ->orderBy('state_name')
                ->pluck('name')
                ->toArray();

            $filterOptions = [
                'anchals' => $anchals,
                'states' => $states
            ];

            // Cache for 2 hours (data changes less frequently)
            cache([$cacheKey => $filterOptions], now()->addHours(2));

            return response()->json([
                'success' => true,
                'data' => $filterOptions
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching filter options: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by anchal (lazy load for performance)
     */
    public function getCitiesByAnchal(Request $request)
    {
        try {
            $anchalId = $request->input('anchal_id');
            
            // Cache key for cities by anchal
            $cacheKey = 'cities_anchal_' . ($anchalId ?? 'all');
            
            // Try cache first
            $cachedCities = cache($cacheKey);
            if ($cachedCities !== null) {
                return response()->json([
                    'success' => true,
                    'cities' => $cachedCities
                ], 200);
            }

            $query = DB::table('cities')
                ->select('city_id as id', 'city_name as name', 'state_id', 'anchal_id');
            
            if ($anchalId) {
                $query->where('anchal_id', $anchalId);
            }
            
            $cities = $query->orderBy('city_name')->get()->toArray();

            // Cache for 1 hour
            cache([$cacheKey => $cities], now()->addHour());

            return response()->json([
                'success' => true,
                'cities' => $cities
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cities: ' . $e->getMessage()
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
            
            // Filter by type if provided
            if ($type) {
                $query->where('type', $type);
            }

            // Get existing designations with their anchal
            // We return ALL designations for this Session+Type combination so the frontend can determine:
            // 1. "Global" uniqueness (e.g. Sanyojak - 1 per Type per Session)
            // 2. "Local" uniqueness (e.g. President - 1 per Anchal per Session)
            $existingDesignations = $query->get(['designation', 'anchal_name']);

            return response()->json([
                'success' => true,
                'combinations' => $existingDesignations,
                'count' => $existingDesignations->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching existing combinations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check duplicate entry by session and MID
     */
    public function checkDuplicate(Request $request)
    {
        try {
            $session = $request->input('session');
            $mid = $request->input('mid');

            if (!$session || !$mid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Both session and MID are required'
                ], 400);
            }

            $member = AddMahilaSamitiMembers::where('session', $session)
                ->where('mid', $mid)
                ->first();

            if ($member) {
                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'member' => $member
                ], 200);
            }

            return response()->json([
                'success' => true,
                'exists' => false
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking duplicate: ' . $e->getMessage()
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

    /**
     * Get designations by designation type
     * Filters designations where designation_dept is 'mahila_samiti' or 'all'
     */
    public function getDesignationsByType(Request $request)
    {
        try {
            $designationTypeId = $request->input('designation_type_id');
            
            if (!$designationTypeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Designation type ID is required'
                ], 400);
            }

            // Get designations for the specified type where dept is mahila_samiti or all
            $designations = Designation::select('id', 'name', 'designation_type_id', 'designation_dept')
                ->where('designation_type_id', $designationTypeId)
                ->whereIn('designation_dept', ['mahila_samiti', 'all'])
                ->orderBy('name')
                ->get()
                ->toArray();

            return response()->json([
                'success' => true,
                'designations' => $designations
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching designations: ' . $e->getMessage()
            ], 500);
        }
    }
}