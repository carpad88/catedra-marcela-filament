<?php

namespace App\Models;

use App\Enums\Visibility;
use App\Traits\CrudBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Work extends Model
{
    use CrudBy, HasFactory;

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'visibility' => Visibility::class,
            'finished' => 'datetime',
            'started' => 'datetime',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::created(function ($work) {
            $group = $work->group;
            $user = $work->user;
            $project = $work->project;

            $work->folder = "$group->folderName/$user->folderName/$project->folderName";
            $work->save();

            if (! Storage::exists($work->folder)) {
                Storage::disk('public')->makeDirectory($work->folder);
            }
        });

        static::updated(function ($work) {
            if ($work->isDirty('cover')) {
                $oldCover = $work->getOriginal('cover');
                if ($oldCover && Storage::disk('public')->exists($oldCover)) {
                    Storage::disk('public')->delete($oldCover);
                }
            }

            if ($work->isDirty('images')) {
                $oldImages = $work->getOriginal('images') ?? [];
                $newImages = $work->images ?? [];

                $imagesToDelete = array_diff($oldImages, $newImages);

                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }
        });
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

    public function scopeRandomPublic($query, $limit = 3, $category = null)
    {
        if (is_null($category)) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereHas('project', function ($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->where('visibility', Visibility::Public)
            ->inRandomOrder()
            ->limit($limit);
    }

    public function getStartedAttribute(): ?Carbon
    {
        $date = $this->project?->groups->firstWhere('id', $this->group_id)?->pivot->started_at;

        return $date ? Carbon::parse($date) : null;
    }

    public function getFinishedAttribute(): ?Carbon
    {
        $date = $this->project?->groups->firstWhere('id', $this->group_id)?->pivot->finished_at;

        return $date ? Carbon::parse($date) : null;
    }
}
