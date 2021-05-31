<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'operation_type',
        'currency_from',
        'currency_to',
        'value_from',
        'value_to'
    ];

    public function saveLog(
        $user_id,
        $operation_type,
        $currency_from,
        $currency_to,
        $value_from,
        $value_to
    )
    {
        self::insert([
            'user_id'           => $user_id,
            'operation_type'    => $operation_type,
            'currency_from'     => $currency_from,
            'currency_to'       => $currency_to,
            'value_from'        => $value_from,
            'value_to'          => $value_to,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
    }
}