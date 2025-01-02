<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\CrudBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use CrudBy, HasFactory;

    protected function casts(): array
    {
        return [
            'status' => Status::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
            ->whereHas('roles', fn ($query) => $query->where('name', 'student'));
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }

    public function scopeCurrentCycle($query)
    {
        return $query->where([
            ['year', '=', now()->year],
            ['cycle', '=', now()->month >= 1 && now()->month <= 6 ? 'A' : 'B'],
        ]);
    }

    public function scopeOwned($query)
    {
        if (auth()->user()->hasRole('super_admin')) {
            return $query
                ->orderBy('year', 'desc')
                ->orderBy('cycle', 'desc');
        }

        return $query->where('owner_id', auth()->id())
            ->orderBy('year', 'desc')
            ->orderBy('cycle', 'desc');
    }

    public function getFolderNameAttribute(): string
    {
        return $this->year.$this->cycle;
    }
}
