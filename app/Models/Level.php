<?php

namespace App\Models;

use App\Traits\CrudBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Level extends Model
{
    use CrudBy, HasFactory;

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }

    public function works(): BelongsToMany
    {
        return $this->belongsToMany(Work::class);
    }
}
