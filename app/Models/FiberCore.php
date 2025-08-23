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

    public function cable()
    {
        return $this->belongsTo(Cable::class);
    }

    public function connectionA()
    {
        return $this->hasOne(CoreConnection::class, 'core_a_id');
    }

    public function connectionB()
    {
        return $this->hasOne(CoreConnection::class, 'core_b_id');
    }

    public function getConnectionAttribute()
    {
        return $this->connectionA ?? $this->connectionB;
    }

    public function isConnected()
    {
        return $this->connectionA || $this->connectionB;
    }
}