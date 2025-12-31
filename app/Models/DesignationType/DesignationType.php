<?php

namespace App\Models\DesignationType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationType extends Model
{
    use HasFactory;

    protected $table = 'designation_types';

    protected $fillable = [
        'name',
        'type'
    ];

    /**
     * Get available type options
     */
    public static function getTypeOptions(): array
    {
        return [
            'shree_sangh' => 'श्री संघ',
            'mahila_samiti' => 'महिला समिति',
            'yuva_sangh' => 'युवा संघ',
            'for_all' => 'सभी के लिए'
        ];
    }
}
