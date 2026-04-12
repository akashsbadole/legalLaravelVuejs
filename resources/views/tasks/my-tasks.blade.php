<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Tasks') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Case</th>
                                <th class="px-4 py-3">Due Date</th>
                                <th class="px-4 py-3">Priority</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $task->isOverdue() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ Str::limit($task->title, 25) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('cases.show', $task->case) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                            {{ $task->case->case_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                        {{ $task->due_date?->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($task->priority === 'urgent')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">Urgent</span>
                                        @elseif($task->priority === 'high')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300">High</span>
                                        @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">Low</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($task->status === 'completed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Completed</span>
                                        @elseif($task->status === 'in_progress')
                                        <span class="px-2 py-1 text-xs rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">In Progress</span>
                                        @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                                            <button type="submit" class="px-3 py-1.5 text-xs rounded-lg {{ $task->status === 'completed' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white transition">
                                                {{ $task->status === 'completed' ? 'Undo' : 'Complete' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No tasks assigned to you.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tasks->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $tasks->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>