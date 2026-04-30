<x-mail::message>
# New Message Received

Hello,

You have received a new message from **{{ $senderName }}** regarding the listing **{{ $advertTitle }}**.

<x-mail::button :url="route('messages.index')">
View Message
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
