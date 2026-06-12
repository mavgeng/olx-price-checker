<x-mail::message>
# Price update

The price for **{{ $advert->title }}** has changed.

Old price: **{{ $oldPrice / 100}} {{ $advert->currency }}**<br>
New price: **{{ $newPrice / 100 }} {{ $advert->currency }}**

<x-mail::button :url="$advert->url">
View advert
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
