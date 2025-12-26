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
        'background_color',
        'card_background_color',
        'text_primary_color',
        'text_secondary_color',
        'text_on_primary_color',
        'success_color',
        'warning_color',
        'danger_color',
        'accent_color',
        'border_color',
        'background_pattern',
        'primary_color_dark',
        'secondary_color_dark',
        'background_color_dark',
        'card_background_color_dark',
        'text_primary_color_dark',
        'text_secondary_color_dark',
        'text_on_primary_color_dark',
        'success_color_dark',
        'warning_color_dark',
        'danger_color_dark',
        'accent_color_dark',
        'border_color_dark',
    ];

    /**
     * Get the partner that owns the identity.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
