<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $external_id
 * @property string $url
 * @property string $title
 * @property int $last_price
 * @property string $currency
 * @property \Illuminate\Support\Carbon|null $last_checked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdvertSubscription> $advertSubscriptions
 * @property-read int|null $advert_subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereLastCheckedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereLastPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advert whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdvert {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $subscriber_id
 * @property string $advert_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Advert $advert
 * @property-read \App\Models\Subscriber $subscriber
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription whereAdvertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdvertSubscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdvertSubscription {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $email
 * @property string|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdvertSubscription> $advertSubscriptions
 * @property-read int|null $advert_subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscriber whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubscriber {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

