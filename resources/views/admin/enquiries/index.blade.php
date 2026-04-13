<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Enquiry Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div class="min-w-[150px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">All Status</option>
                            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="min-w-[150px]">
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">All Priorities</option>
                            <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="emergency" {{ request('priority') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                    </div>

                    <div class="min-w-[150px]">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigned To</label>
                        <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">All Lawyers</option>
                            @foreach($lawyers as $lawyer)
                            <option value="{{ $lawyer->id }}" {{ request('assigned_to') == $lawyer->id ? 'selected' : '' }}>
                                {{ $lawyer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                        <a href="{{ route('admin.enquiries.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-5.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Enquiries</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $enquiries->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Enquiries</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Enquiry::where('status', 'new')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgent Enquiries</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Enquiry::whereIn('priority', ['urgent', 'emergency'])->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Resolved</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Enquiry::whereIn('status', ['resolved', 'closed'])->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enquiries Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Enquiries</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Client</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Category</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Priority</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Assigned To</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Created</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($enquiries as $enquiry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $enquiry->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $enquiry->email }}</div>
                                    @if($enquiry->phone)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $enquiry->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full">
                                        {{ $enquiry->getCategoryDisplay() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $enquiry->getPriorityColor() }}">
                                        {{ ucfirst($enquiry->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $enquiry->getStatusColor() }}">
                                        {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ $enquiry->assignedUser?->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $enquiry->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                        @if($enquiry->status === 'new')
                                        <form method="POST" action="{{ route('admin.enquiries.assign', $enquiry) }}" class="inline">
                                            @csrf
                                            <select name="assigned_to" onchange="this.form.submit()" class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700">
                                                <option value="">Assign to...</option>
                                                @foreach($lawyers as $lawyer)
                                                <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No enquiries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($enquiries->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $enquiries->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>