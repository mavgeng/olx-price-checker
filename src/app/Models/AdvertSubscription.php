<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAdvertSubscription
 */
#[Fillable(['subscriber_id', 'advert_id'])]
class AdvertSubscription extends Model
{
    use HasUlids;

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class);
    }
}
