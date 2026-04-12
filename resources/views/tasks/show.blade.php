<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Task Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Case: <a href="{{ route('cases.show', $task->case) }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $task->case->case_number }}</a></p>
                        </div>
                        <div class="flex space-x-2">
                            @if($task->priority === 'urgent')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">Urgent</span>
                            @elseif($task->priority === 'high')
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300">High</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">Normal</span>
                            @endif
                            @if($task->status === 'completed')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Completed</span>
                            @elseif($task->status === 'in_progress')
                            <span class="px-2 py-1 text-xs rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">In Progress</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">Pending</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                            <p class="text-gray-900 dark:text-white">{{ $task->assignedTo->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                            <p class="text-gray-900 dark:text-white">{{ $task->createdBy->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
                            <p class="text-gray-900 dark:text-white">{{ $task->due_date?->format('M d, Y') ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                            <p class="text-gray-900 dark:text-white">{{ $task->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                        <p class="text-gray-900 dark:text-white">{{ $task->description ?? 'No description' }}</p>
                    </div>

                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                            <button type="submit" class="px-4 py-2 {{ $task->status === 'completed' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg">
                                {{ $task->status === 'completed' ? 'Mark Incomplete' : 'Mark Complete' }}
                            </button>
                        </form>
                        <a href="{{ route('tasks.edit', $task) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Edit</a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comments ({{ $task->comments->count() }})</h3>
                </div>
                <div class="p-5">
                    @forelse($task->comments as $comment)
                    <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                        <div class="flex items-center mb-2">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $comment->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">{{ $comment->content }}</p>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400">No comments yet.</p>
                    @endforelse

                    <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="mt-4">
                        @csrf
                        <textarea name="content" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Add a comment..." required></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-primary-600 dark:bg-primary-500 text-white rounded-lg hover:bg-primary-700 dark:hover:bg-primary-600">Post Comment</button>
                    </form>
                </div>
            </div>

            <a href="{{ route('tasks.index') }}" class="inline-block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                Back to Tasks
            </a>
        </div>
    </div>
</x-app-layout>