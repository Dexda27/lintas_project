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
        'source_site_id',
        'destination_site_id',
        'description',
    ];

    public function sourceSite()
    {
        return $this->belongsTo(Site::class, 'source_site_id');
    }

    public function destinationSite()
    {
        return $this->belongsTo(Site::class, 'destination_site_id');
    }

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