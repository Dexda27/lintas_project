<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'node'; // Sesuaikan jika nama tabel berbeda

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_node',
    ];

    /**
     * Get the svlans associated with the node.
     */
    public function svlans()
    {
        return $this->hasMany(Svlan::class, 'node_id');
    }
}