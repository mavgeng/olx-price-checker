<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperAdvert
 */
#[Fillable(['external_id', 'url', 'title', 'last_price', 'currency', 'is_active', 'last_checked_at'])]
class Advert extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return [
            'last_price' => 'integer',
            'is_active' => 'boolean',
            'last_checked_at' => 'datetime',
        ];
    }

    public function advertSubscriptions(): HasMany
    {
        return $this->hasMany(AdvertSubscription::class);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'advert_subscriptions');
    }
}
