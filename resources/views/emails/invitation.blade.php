<x-mail::message>
# You've been invited!

You have been invited to join **{{ config('app.name') }}** as a **{{ $invitation->role }}**.

<x-mail::button :url="$url">
Accept Invitation
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

This invitation will expire on {{ $invitation->expires_at->format('F j, Y, g:i a') }}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
