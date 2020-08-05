<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'datetime',
        'amount',
        'type'
    ];

    protected $visible = [
        'datetime',
        'amount',
        'type'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
