<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lead: ' . $lead->name) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Lead Details</h3>
                            <a href="{{ route('leads.edit', $lead) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Edit</a>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="text-gray-900 dark:text-white">{{ $lead->email ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                    <p class="text-gray-900 dark:text-white">{{ $lead->phone ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Company</p>
                                    <p class="text-gray-900 dark:text-white">{{ $lead->company ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Source</p>
                                    <p class="text-gray-900 dark:text-white">{{ ucfirst($lead->source ?? '—') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($lead->status === 'new') bg-blue-100 text-blue-800
                                        @elseif($lead->status === 'contacted') bg-yellow-100 text-yellow-800
                                        @elseif($lead->status === 'qualified') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Priority</p>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($lead->priority === 'high') bg-red-100 text-red-800
                                        @elseif($lead->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($lead->priority ?? 'low') }}
                                    </span>
                                </div>
                            </div>
                            @if($lead->description)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                                <p class="text-gray-900 dark:text-white">{{ $lead->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Activities</h3>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('leads.activities.store', $lead) }}" class="mb-4 flex gap-2">
                                @csrf
                                <select name="activity_type" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                    <option value="call">Phone Call</option>
                                    <option value="email">Email</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="note">Note</option>
                                </select>
                                <input type="text" name="notes" placeholder="Add note..." class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Add</button>
                            </form>
                            <div class="space-y-4">
                                @forelse($lead->activities()->orderBy('created_at', 'desc')->take(10)->get() as $activity)
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs text-primary-600 dark:text-primary-400">{{ substr($activity->user?->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            <span class="font-medium">{{ $activity->user?->name ?? 'Unknown' }}</span>
                                            <span class="text-gray-500"> - {{ $activity->activity_type }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->notes }}</p>
                                        <p class="text-xs text-gray-400">{{ $activity->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No activities yet</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($lead->status !== 'won' && $lead->status !== 'lost')
                            <form method="POST" action="{{ route('leads.convert', $lead) }}">
                                @csrf
                                <input type="text" name="case_title" placeholder="Case title" class="w-full mb-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm">
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Convert to Case</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('leads.update', $lead) }}">
                                @csrf
                                @method('PUT')
                                <select name="status" class="w-full mb-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm">
                                    <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="proposal" {{ $lead->status === 'proposal' ? 'selected' : '' }}>Proposal</option>
                                    <option value="won" {{ $lead->status === 'won' ? 'selected' : '' }}>Won</option>
                                    <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                                <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Update Status</button>
                            </form>
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete Lead</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>