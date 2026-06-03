<x-mail::message>
# New Task Assigned

Hello {{ $user->name }},

You have been assigned a new task: **{{ $task['title'] }}**.

@if(isset($task['due_date']))
**Due Date:** {{ $task['due_date'] }}
@endif

<x-mail::button :url="config('app.url') . '/#/tasks'">
View Tasks
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
