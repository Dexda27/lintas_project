<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreConnection extends Model
{
    use HasFactory;

    protected $table = 'core_connections';
    protected $fillable = [
        'closure_id',
        'core_a_id',
        'core_b_id',
        'splice_loss',
        'description',
    ];

    public function closure()
    {
        return $this->belongsTo(JointClosure::class, 'closure_id');
    }

    public function coreA()
    {
        return $this->belongsTo(FiberCore::class, 'core_a_id');
    }

    public function coreB()
    {
        return $this->belongsTo(FiberCore::class, 'core_b_id');
    }
}