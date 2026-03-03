<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
