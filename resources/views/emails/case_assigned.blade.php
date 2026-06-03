<x-mail::message>
# New Case Assigned

Hello {{ $user->name }},

You have been assigned to a new case: **{{ $case['title'] }}** ({{ $case['case_number'] }}).

<x-mail::button :url="config('app.url') . '/#/cases/' . $case['id']">
View Case
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
