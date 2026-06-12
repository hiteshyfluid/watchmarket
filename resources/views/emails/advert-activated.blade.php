<x-mail::message>
# Your advert is now live

Hi {{ $order->user?->first_name ?: 'there' }},

Your advert **{{ $advert?->title ?? 'Watch listing' }}** has been published on Watch Market.

<x-mail::panel>
Advert: {{ $advert?->title ?? '-' }}

Package: {{ $order->level?->name ?? '-' }}

Order code: {{ $order->code ?: $order->id }}

Amount paid: &pound;{{ number_format((float) $order->total, 2) }}

Payment method: {{ $order->gateway ?: 'Manual Checkout' }}

Transaction: {{ $order->payment_transaction_id ?: 'N/A' }}

Activated on: {{ ($order->ordered_at ?? $order->created_at)?->format('F j, Y g:i A') }}
</x-mail::panel>

<x-mail::button :url="$advertUrl">
View Advert
</x-mail::button>

Your invoice is attached to this email. You can also review your listing purchases from your Watch Market account.

Thanks,<br>
Watch Market
</x-mail::message>
