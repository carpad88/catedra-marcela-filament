<?php

namespace App\Models;

use App\Traits\CrudBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use CrudBy, HasFactory;

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
        return $this->belongsToMany(User::class)
            ->whereHas('roles', fn ($query) => $query->where('name', 'student'));
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
}
