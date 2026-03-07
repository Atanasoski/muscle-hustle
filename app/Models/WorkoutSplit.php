<?php

namespace App\Models;

use App\Enums\SplitFocus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutSplit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'days_per_week',
        'focus',
        'day_index',
        'target_regions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'focus' => SplitFocus::class,
            'target_regions' => 'array',
            'days_per_week' => 'integer',
            'day_index' => 'integer',
        ];
    }

    /**
     * Get workout split for given days per week and focus.
     *
     * @return array<int, array<int, string>>
     */
    public static function getSplit(int $daysPerWeek, SplitFocus $focus): array
    {
        return static::query()
            ->where('days_per_week', $daysPerWeek)
            ->where('focus', $focus)
            ->orderBy('day_index')
            ->pluck('target_regions')
            ->toArray();
    }
}
