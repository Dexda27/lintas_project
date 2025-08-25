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
        'source_site',      // Changed from source_site_id
        'destination_site', // Changed from destination_site_id
        'description',
    ];

    // Removed sourceSite() and destinationSite() relationships
    // since we're now using text fields instead of foreign keys

    public function fiberCores()
    {
        return $this->hasMany(FiberCore::class);
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