<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthAccount extends Model
{
    protected $fillable = [
        'user_id','provider','provider_user_id',
        'access_token','refresh_token','expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
