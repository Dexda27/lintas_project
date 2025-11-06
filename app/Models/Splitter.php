<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Splitter extends Model
{
    use HasFactory;

    protected $fillable = [
        'splitter_id',
        'name',
        'location',
        'region',
        'capacity',
        'used_capacity',
        'status',
        'latitude',
        'longitude',
        'description',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->used_capacity;
    }
}