<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiberCore extends Model
{
    use HasFactory;

    protected $fillable = [
        'cable_id',
        'tube_number',
        'core_number',
        'status',
        'usage',
        'attenuation',
        'description',
    ];
    // Add these methods to your existing FiberCore model

    public function connectionA()
    {
        return $this->hasOne(CoreConnection::class, 'core_a_id');
    }

    public function connectionB()
    {
        return $this->hasOne(CoreConnection::class, 'core_b_id');
    }

    // Remove the connection() method as it causes issues with Laravel relationships
    // Instead, use these methods to get connection data:

    public function getConnectionAttribute()
    {
        // This returns the actual connection record, not a relationship
        return $this->connectionA ?: $this->connectionB;
    }

    public function isConnected()
    {
        return $this->connectionA()->exists() || $this->connectionB()->exists();
    }

    public function getConnectedCore()
    {
        if ($this->connectionA) {
            return $this->connectionA->coreB;
        } elseif ($this->connectionB) {
            return $this->connectionB->coreA;
        }
        return null;
    }

    public function cable()
    {
        return $this->belongsTo(Cable::class);
    }
}
