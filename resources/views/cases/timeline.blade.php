<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Case Timeline') }}
            </h2>
            <a href="{{ route('cases.show', $case) }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Back to Case
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Event</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('cases.timeline.store', $case) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                                <input type="text" name="title" id="title" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            </div>
                            <div>
                                <label for="event_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Type *</label>
                                <select name="event_type" id="event_type" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                    @foreach($eventTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="event_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date *</label>
                                <input type="date" name="event_date" id="event_date" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_milestone" id="is_milestone" value="1" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                <label for="is_milestone" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark as milestone</label>
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea name="description" id="description" rows="2" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Add Event</button>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline</h3>
                </div>
                <div class="p-6">
                    @php
                        $groupedEvents = $case->timelineEvents->groupBy(function($event) {
                            return $event->event_date->format('F Y');
                        });
                    @endphp
                    
                    @forelse($groupedEvents as $month => $events)
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">{{ $month }}</h4>
                            <div class="space-y-4">
                                @foreach($events as $event)
                                    <div class="relative pl-8 {{ $event->is_milestone ? 'bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4 -ml-2' : '' }}">
                                        <div class="absolute left-0 top-0 w-4 h-4 rounded-full {{ $event->is_milestone ? 'bg-primary-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $event->title }}</span>
                                                    @if($event->is_milestone)
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">Milestone</span>
                                                    @endif
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 mt-1">
                                                    {{ \App\Models\CaseTimelineEvent::getEventTypes()[$event->event_type] ?? $event->event_type }}
                                                </span>
                                                @if($event->description)
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $event->description }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    {{ $event->user->name ?? 'System' }} • {{ $event->event_date->format('M d, Y') }}
                                                </p>
                                            </div>
                                            <form method="POST" action="{{ route('cases.timeline.destroy', $event) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm" onclick="return confirm('Delete this event?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No timeline events yet. Add your first event above.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>