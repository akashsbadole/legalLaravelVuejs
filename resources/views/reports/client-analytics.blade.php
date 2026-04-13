<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Client Analytics & LTV Report') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalClients }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total LTV</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($totalLTV, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg LTV</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($avgLTV, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">High-Value Clients</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $highValueClients }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Details</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Client</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Cases</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Active Cases</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Total Invoiced</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Total Paid</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Outstanding</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">LTV</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Last Case</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $client['name'] }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $client['email'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $client['total_cases'] }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $client['active_cases'] }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">₹{{ number_format($client['total_invoiced'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">₹{{ number_format($client['total_paid'], 2) }}</td>
                                <td class="px-4 py-3 {{ $client['outstanding_balance'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                    ₹{{ number_format($client['outstanding_balance'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">₹{{ number_format($client['ltv'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $client['last_case_date'] ? $client['last_case_date']->format('M d, Y') : '—' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No clients found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>