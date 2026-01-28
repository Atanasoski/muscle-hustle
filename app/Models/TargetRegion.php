<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TargetRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'display_order',
    ];

    /**
     * Relationship: TargetRegion has many Exercises
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
}
