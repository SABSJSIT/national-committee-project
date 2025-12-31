<?php

namespace App\Models\Session;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'sessions';

    protected $fillable = [
        'name',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
