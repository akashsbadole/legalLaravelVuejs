<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cases') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <form method="GET" class="p-4 flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by case number or title..." 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Filter</button>
                    <a href="{{ route('cases.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Reset</a>
                    <a href="{{ route('cases.create') }}" class="ml-auto px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">+ New Case</a>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-gray-600">
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Case #</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Title</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Attorney</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Client</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Open Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($cases as $case)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="case-checkbox rounded border-gray-300 dark:border-gray-600" value="{{ $case->id }}">
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('cases.show', $case) }}" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">
                                        {{ $case->case_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ Str::limit($case->title, 35) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($case->status?->name === 'Open') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($case->status?->name === 'Closed') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @elseif($case->status?->name === 'Pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 @endif">
                                        {{ $case->status?->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $case->assignedAttorney?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $case->client?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $case->open_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('cases.show', $case) }}" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">View</a>
                                        <a href="{{ route('cases.edit', $case) }}" class="text-gray-600 dark:text-gray-400 hover:underline text-sm">Edit</a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No cases found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cases->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $cases->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div id="bulkActions" class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 hidden">
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600 dark:text-gray-400"><span id="selectedCount">0</span> selected</span>
            <button onclick="bulkAction('export')" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Export</button>
            <button onclick="bulkAction('status')" class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">Change Status</button>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.case-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        document.querySelectorAll('.case-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const selected = document.querySelectorAll('.case-checkbox:checked').length;
            const bulkDiv = document.getElementById('bulkActions');
            document.getElementById('selectedCount').textContent = selected;
            bulkDiv.classList.toggle('hidden', selected === 0);
        }

        function bulkAction(action) {
            const selected = Array.from(document.querySelectorAll('.case-checkbox:checked')).map(cb => cb.value);
            if (action === 'export') {
                window.location.href = '{{ route("export.cases") }}?ids=' + selected.join(',');
            } else if (action === 'status') {
                alert('Status change coming soon!');
            }
        }
    </script>
</x-app-layout>