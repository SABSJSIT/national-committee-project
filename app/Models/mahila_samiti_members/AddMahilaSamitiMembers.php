<?php

namespace App\Models\mahila_samiti_members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddMahilaSamitiMembers extends Model
{
    use HasFactory;

    protected $table = 'mahila_samiti_members';

    protected $fillable = [
        'session',
        'anchal_name',
        'anchal_code',
        'type',
        'designation',
        'mid',
        'name',
        'name_hindi',
        'husband_name',
        'husband_name_hindi',
        'father_name',
        'father_name_hindi',
        'address',
        'address_hindi',
        'city',
        'state',
        'pincode',
        'mobile_number',
        'wtp_number',
        'photo',
        'ex_post',
        'remarks'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Validation rules
    public static function validationRules($id = null)
    {
        return [
            'session' => 'required|string|max:50',
            'anchal_name' => 'required|string|max:100',
            'anchal_code' => 'required|string|max:50',
            'type' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'mid' => 'required|string|max:6',
            'name' => 'required|string|max:100',
            'husband_name' => 'nullable|required_without:father_name|string|max:100',
            'father_name' => 'nullable|required_without:husband_name|string|max:100',
            'address' => 'nullable|string',
            'city' => 'required|string|max:50',
            'state' => 'required|string|max:50',
            'pincode' => 'nullable|string|max:10',
            'mobile_number' => 'required|string|max:15',
            'wtp_number' => 'nullable|string|max:15',
            'photo' => 'nullable|string',
            'ex_post' => 'nullable|string|max:100',
            'remarks' => 'nullable|string'
        ];
    }

    // Custom validation messages
    public static function validationMessages()
    {
        return [
            'session.required' => 'Session field is required.',
            'anchal_name.required' => 'Anchal name is required.',
            'anchal_code.required' => 'Anchal code is required.',
            'type.required' => 'Type field is required.',
            'type.in' => 'Please select a valid type.',
            'designation.required' => 'Designation is required.',
            'mid.required' => 'MID is required.',
            'name.required' => 'Name is required.',
            'husband_name.required_without' => 'Either husband name or father name is required.',
            'father_name.required_without' => 'Either father name or husband name is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'mobile_number.required' => 'Mobile number is required.',
        ];
    }

    // Check if entry already exists for session
    public static function sessionEntryExists($session, $mid, $id = null)
    {
        $query = self::where('session', $session)
                    ->where('mid', $mid);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return $query->exists();
    }

    // Check if type-designation combination already exists for session
    public static function typeDesignationExists($session, $type, $designation, $id = null)
    {
        $query = self::where('session', $session)
                    ->where('type', $type)
                    ->where('designation', $designation);
        
        if ($id) {
            $query->where('id', '!=', $id);
        }
        
        return $query->exists();
    }

    // Check if sanyojika already exists for the given type in current session
    public static function sanyojikaExistsForType($session, $type, $id = null)
    {
        $query = self::where('session', $session)
                    ->where('type', $type)
                    ->where(function($q) {
                        $q->where('designation', 'like', '%संयोजक%')
                          ->orWhere('designation', 'like', '%संयोजिका%')
                          ->orWhere('designation', 'like', '%Sanyojak%')
                          ->orWhere('designation', 'like', '%Sanyojika%');
                    });
        
        if ($id) {
            $query->where('id', '!=', $id);
        }
        
        return $query->exists();
    }
}