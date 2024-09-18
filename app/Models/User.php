<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use BezhanSalleh\FilamentShield\Support\Utils;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\Hasroles;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return str_ends_with($this->email, '@admin.com') && $this->hasVerifiedEmail();
    // }

    protected $fillable = [
        'name',
        'email',
        'password',
        'location_id',
        'no_hp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function Location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected static function booted(): void
    {
        if (config('filament-shield.sales.enabled', false)) {
            FilamentShield::createRole(name: config('filament-shield.sales.name', 'sales' ));
            static::created(function (User $user) {
                $user->assignRole(config('filament-shield.sales.name', 'sales'));
            });
            static::deleting(function (User $user) {
                $user->removeRole(config('filament-shield.sales.name', 'sales'));
            });
        }
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if( $panel->getId() === 'admin'){
            return $this->hasRole(Utils::getSuperAdminName());
        }elseif($panel->getId() === 'sales'){
            return $this->hasRole(config('filament-shield.sales.name', 'sales'));
        }else{
            return false;
        }
    }

    // public function canAccessFilamentShield(): bool
    // {
    //     // Cek apakah user memiliki role superadmin
    //     return $this->hasRole(Utils::getSuperAdminName());
    // }
}
