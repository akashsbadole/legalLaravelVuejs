<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Client Activity Report') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Interaction Summary</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Client</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Total Interactions</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Last Interaction</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Interaction Types</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Recent Activity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $client['name'] }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $client['email'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $client['total_interactions'] }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $client['last_interaction'] ? $client['last_interaction']->diffForHumans() : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($client['interaction_types'] as $type)
                                        <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full">
                                            {{ ucfirst($type) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($client['recent_activity']->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($client['recent_activity'] as $activity)
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($activity->type) }}</span>
                                                <span class="text-gray-500 dark:text-gray-400">- {{ $activity->created_at->diffForHumans() }}</span>
                                                @if($activity->subject)
                                                <div class="text-gray-600 dark:text-gray-300">{{ Str::limit($activity->subject, 30) }}</div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">No recent activity</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No clients found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>