<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'browser',
        'platform',
        'ip_address',
        'trusted_at',
    ];
}
