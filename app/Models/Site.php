<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region',
        'latitude',
        'longitude',
        'description',
    ];

    public function sourceCables()
    {
        return $this->hasMany(Cable::class, 'source_site_id');
    }

    public function destinationCables()
    {
        return $this->hasMany(Cable::class, 'destination_site_id');
    }
}