<?php

namespace App\Models;

use App\Traits\CrudBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use CrudBy, HasFactory;

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
