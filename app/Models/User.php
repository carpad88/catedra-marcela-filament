<?php

namespace App\Models;

use App\Traits\CrudBy;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    use CrudBy, HasFactory, HasRoles, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string
    {
        return "$this->first_name $this->last_name";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'app') {
            return $this->hasAnyRole(['student', 'teacher', 'super_admin']);
        }

        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['teacher', 'super_admin']);
        }

        return false;
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->orderBy('year', 'desc')
            ->orderBy('cycle', 'desc');
    }

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }

    public function getFolderNameAttribute(): string
    {
        return $this->id.'-'.str("$this->first_name $this->last_name")->slug()->studly();
    }
}
