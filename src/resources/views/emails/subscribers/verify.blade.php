<x-mail::message>
# Confirm your subscription

You subscribed to OLX price alerts using **{{ $email }}**.

First verify your email using the button below.

<x-mail::button :url="$verifyUrl">
Verify email
</x-mail::button>

Then we'll let you know as soon as the price changes.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
