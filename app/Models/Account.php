<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'default_currency',
        'balance'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}