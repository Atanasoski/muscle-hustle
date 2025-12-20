<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the workout templates for the user.
     */
    public function workoutTemplates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkoutTemplate::class);
    }

    /**
     * Get the workout sessions for the user.
     */
    public function workoutSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }

    /**
     * Get the meal plans for the user.
     */
    public function mealPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    /**
     * Get the custom exercises for the user.
     */
    public function exercises(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    /**
     * Get the recipes for the user.
     */
    public function recipes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Get the partner that the user belongs to.
     */
    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
