<?php

namespace App\Models;

use App\Enums\Visibility;
use App\Traits\CrudBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Work extends Model
{
    use CrudBy, HasFactory;

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'visibility' => Visibility::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function rubrics(): BelongsToMany
    {
        return $this->belongsToMany(Criteria::class, 'rubrics', 'work_id', 'criteria_id')
            ->withPivot('level_id');
    }

    public function scores()
    {
        return $this->hasMany(Rubric::class)->with('level:id,score');
    }
}
