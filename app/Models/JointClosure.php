<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JointClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'closure_id',
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

    public function coreConnections()
    {
        return $this->hasMany(CoreConnection::class, 'closure_id');
    }

    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->used_capacity;
    }
}