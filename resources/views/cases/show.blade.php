<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Case: ') . $case->case_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $case->title }}</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('cases.timeline', $case) }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Timeline</a>
                    <a href="{{ route('cases.edit', $case) }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Edit</a>
                    <form method="POST" action="{{ route('cases.destroy', $case) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this case?')" class="px-4 py-2 rounded-xl bg-red-600 dark:bg-red-500 text-white hover:bg-red-700 dark:hover:bg-red-600 transition">Delete</button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $case->status->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->case_type ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Attorney</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->assignedAttorney->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Open Date</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->open_date?->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Expected Close</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->expected_close_date?->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Estimated Value</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->estimated_value ? '$' . number_format($case->estimated_value, 2) : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                            <p class="text-gray-900 dark:text-white">{{ $case->description ?? 'No description' }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notes</h3>
                        </div>
                        <div class="p-6">
                            @forelse(($case->notes ?? collect())->sortByDesc('created_at') as $note)
                            <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $note->user->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $note->created_at?->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300">{{ $note->content }}</p>
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400">No notes yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Client</span>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $case->client_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $case->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>