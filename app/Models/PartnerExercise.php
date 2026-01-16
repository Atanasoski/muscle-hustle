<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PartnerExercise extends Pivot
{
    protected $fillable = [
        'partner_id',
        'exercise_id',
        'description',
        'image',
        'video',
    ];
}
