<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pole extends Model
{
    protected $fillable = [
        'pole_id',
        'name',
        'location',
        'region',
        'type',
        'height',
        'latitude',
        'longitude',
        'description',
        'status'
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    /**
     * Get the joint closures connected to this pole
     *
     * @return BelongsToMany<JointClosure>
     */
    public function jointClosures(): BelongsToMany
    {
        return $this->belongsToMany(JointClosure::class, 'pole_joint_closure')
            ->withTimestamps();
    }

    /**
     * Get the splitters connected to this pole
     *
     * @return BelongsToMany<Splitter>
     */
    public function splitters(): BelongsToMany
    {
        return $this->belongsToMany(Splitter::class, 'pole_splitter')
            ->withTimestamps();
    }

    /**
     * Generate next pole ID based on region
     *
     * @param string $region
     * @return string
     */
    public static function generatePoleId(string $region): string
    {
        $prefix = 'POLE-' . strtoupper($region) . '-';
        $lastPole = static::where('pole_id', 'like', $prefix . '%')
            ->orderBy('pole_id', 'desc')
            ->first();

        if (!$lastPole) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($lastPole->pole_id, -4);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

    /**
     * Get total equipment count (JCs + Splitters)
     *
     * @return int
     */
    public function getTotalEquipmentAttribute(): int
    {
        return $this->jointClosures()->count() + $this->splitters()->count();
    }
}
