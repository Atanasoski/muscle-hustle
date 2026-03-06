<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingStyle extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'display_order',
    ];

    /**
     * Relationship: TrainingStyle belongs to many Exercises
     */
    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercise_training_style');
    }
}
