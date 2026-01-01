<?php

namespace App\Models\Designation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DesignationType\DesignationType;

class Designation extends Model
{
    use HasFactory;

    protected $table = 'designations';

    protected $fillable = [
        'name',
        'designation_type_id',
        'designation_dept'
    ];

    /**
     * Get the designation type that owns the designation
     */
    public function designationType()
    {
        return $this->belongsTo(DesignationType::class, 'designation_type_id');
    }
}
