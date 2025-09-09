<?php
// file: app/Models/Cvlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cvlan extends Model
{
    use HasFactory;

    protected $table = 'cvlan';

    // TAMBAHKAN 'nms' PADA BARIS INI
    protected $fillable = [
        'svlan_id', 'node_id', 'cvlan_slot', 'nms', 'metro', 'vpn', 'inet', 'extra', 'no_jaringan', 'nama_pelanggan'
    ];

    public function svlan()
    {
        return $this->belongsTo(Svlan::class, 'svlan_id');
    }

    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id');
    }
}