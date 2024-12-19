<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\CrudBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use CrudBy, HasFactory;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'status' => Status::class,
        ];
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function criterias(): HasMany
    {
        return $this->hasMany(Criteria::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }

    public function scopeOwned($query)
    {
        if (auth()->user()->hasRole('super_admin')) {
            return $query;
        }

        return $query->where('owner_id', auth()->id());
    }
}
