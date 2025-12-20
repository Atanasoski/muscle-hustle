<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerIdentity extends Model
{
    protected $fillable = [
        'partner_id',
        'primary_color',
        'secondary_color',
        'logo',
        'font_family',
    ];

    /**
     * Get the partner that owns the identity.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
