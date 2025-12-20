<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the identity for the partner.
     */
    public function identity(): HasOne
    {
        return $this->hasOne(PartnerIdentity::class);
    }

    /**
     * Get all identities for the partner.
     */
    public function identities(): HasMany
    {
        return $this->hasMany(PartnerIdentity::class);
    }

    /**
     * Get the users for the partner.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
