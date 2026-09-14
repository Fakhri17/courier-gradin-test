<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'level',
        'vehicle_type',
        'vehicle_plate_number',
        'address',
        'status',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'registered_at' => 'date',
        ];
    }
}

