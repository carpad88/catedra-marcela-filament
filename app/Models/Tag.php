<?php

namespace App\Models;

use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany|Tag
    {
        return $this->hasMany(Project::class, 'category_id', 'id');
    }

    public function works(): \Illuminate\Database\Eloquent\Relations\hasManyThrough|Work
    {
        return $this->hasManyThrough(Work::class, Project::class, 'category_id', 'project_id', 'id', 'id');
    }
}
