<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Svlan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'svlan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'node_id',
        'svlan_nms',
        'svlan_me',
        'svlan_vpn',
        'svlan_inet',
        'extra',
        'keterangan',
    ];

    /**
     * Get the cvlans for the svlan.
     */
    public function cvlans()
    {
        return $this->hasMany(Cvlan::class, 'svlan_id');
    }

    /**
     * ======================= TAMBAHKAN METHOD INI =======================
     * Get the node that owns the svlan.
     */
    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id');
    }
    // ===================== AKHIR DARI PENAMBAHAN ====================
}