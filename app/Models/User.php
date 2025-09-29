<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The accessors that should be appended to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'is_subscribed',
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'address',
        'city',
        'cp',
        'country',
        'age',
        'password',
        'is_admin',
        'display_name_color',
        'display_alias',
        'profile_border_style',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(GameRating::class);
    }

    public function oauthAccounts()
    {
        return $this->hasMany(OauthAccount::class);
    }

    public function getIsSubscribedAttribute(): bool
    {
        return $this->subscribed('default');
    }

}
