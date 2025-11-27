<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cable extends Model
{
    use HasFactory;

    protected $fillable = [
        'cable_id',
        'name',
        'region',
        'total_tubes',
        'total_cores',
        'cores_per_tube',
        'status',
        'usage',
        'otdr_length',
        'source_site',
        'destination_site',
        'description',
    ];



    public function fiberCores()
    {
        return $this->hasMany(FiberCore::class);
    }

    // Relasi untuk koneksi melalui core A
    public function coreConnectionsA()
    {
        return $this->hasManyThrough(
            CoreConnection::class,
            FiberCore::class,
            'cable_id',      // Foreign key on fiber_cores table
            'core_a_id',     // Foreign key on core_connections table
            'id',            // Local key on cables table
            'id'             // Local key on fiber_cores table
        );
    }

    // Relasi untuk koneksi melalui core B
    public function coreConnectionsB()
    {
        return $this->hasManyThrough(
            CoreConnection::class,
            FiberCore::class,
            'cable_id',      // Foreign key on fiber_cores table
            'core_b_id',     // Foreign key on core_connections table
            'id',            // Local key on cables table
            'id'             // Local key on fiber_cores table
        );
    }

    public function getActiveCoresCountAttribute()
    {
        return $this->fiberCores()->where('usage', 'active')->count();
    }

    public function getInactiveCoresCountAttribute()
    {
        return $this->fiberCores()->where('usage', 'inactive')->count();
    }

    public function getProblemCoresCountAttribute()
    {
        return $this->fiberCores()->where('status', 'not_ok')->count();
    }
}
