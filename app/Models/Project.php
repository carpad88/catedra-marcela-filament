<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\CrudBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        return $this->belongsToMany(Group::class)
            ->withPivot('started_at', 'finished_at');
    }

    public function criterias(): HasMany
    {
        return $this->hasMany(Criteria::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function scopeOwned($query)
    {
        if (auth()->user()->hasRole('super_admin')) {
            return $query;
        }

        return $query->where('owner_id', auth()->id());
    }

    public function getFolderNameAttribute(): string
    {
        return str($this->title)->slug()->studly();
    }

    public function getFinishedAttribute(): ?Carbon
    {
        $finished = $this->groups->intersect(auth()->user()->groups)->first()?->pivot->finished_at;

        return $finished ? Carbon::parse($finished) : now();
    }
}
