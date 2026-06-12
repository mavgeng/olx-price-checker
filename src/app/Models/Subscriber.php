<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSubscriber
 */
#[Fillable(['email', 'email_verified_at'])]
class Subscriber extends Model
{
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function advertSubscriptions(): HasMany
    {
        return $this->hasMany(AdvertSubscription::class);
    }
}
