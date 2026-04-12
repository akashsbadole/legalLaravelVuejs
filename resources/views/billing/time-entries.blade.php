<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Time Entries') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Hours</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($entries->sum('hours'), 1) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Billable Amount</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($entries->where('status', 'approved')->sum(fn($e) => $e->hours * $e->rate), 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $entries->where('status', 'pending')->count() }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <form method="GET" class="p-4 flex flex-wrap gap-4 items-center">
                    <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Filter</button>
                    <a href="{{ route('billing.time-entries') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50">Reset</a>
                    <button @click="$dispatch('open-modal')" class="ml-auto px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">+ Add Entry</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">User</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Case</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Description</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Hours</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Rate</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($entries as $entry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $entry->work_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $entry->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($entry->case)
                                    <a href="{{ route('cases.show', $entry->case) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                        {{ $entry->case->case_number }}
                                    </a>
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ Str::limit($entry->description, 30) }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $entry->hours }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">${{ number_format($entry->rate, 2) }}/hr</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">${{ number_format($entry->hours * $entry->rate, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($entry->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($entry->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($entry->status === 'pending')
                                    <form method="POST" action="{{ route('billing.time-entries.approve', $entry) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-green-600 dark:text-green-400 hover:underline">Approve</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No time entries found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($entries->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $entries->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div x-data="{ open: false }" @open-modal.window="open = true" @keydown.escape.window="open = false">
        <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="open" @click="open = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
                <div x-show="open" class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-xl" @click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Time Entry</h3>
                        <button @click="open = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('billing.time-entries.store') }}" class="p-6">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="case_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Case *</label>
                                <select name="case_id" id="case_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                    <option value="">Select Case</option>
                                    @foreach($cases as $case)
                                    <option value="{{ $case->id }}">{{ $case->case_number }} - {{ $case->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description *</label>
                                <textarea name="description" id="description" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hours *</label>
                                    <input type="number" name="hours" id="hours" step="0.1" min="0.1" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </div>
                                <div>
                                    <label for="work_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                                    <input type="date" name="work_date" id="work_date" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>