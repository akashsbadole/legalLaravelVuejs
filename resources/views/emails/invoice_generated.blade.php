<x-mail::message>
# New Invoice Generated

Hello {{ $user->name }},

A new invoice has been generated for your account: **{{ $invoice['invoice_number'] }}**.

**Amount Due:** {{ $invoice['total_amount'] ?? $invoice['total'] }}
**Due Date:** {{ $invoice['due_date'] }}

<x-mail::button :url="config('app.url') . '/#/invoices'">
View Invoices
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
