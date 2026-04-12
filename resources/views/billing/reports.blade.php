<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl p-6 text-white shadow-lg">
                    <p class="text-sm font-medium text-green-100">Total Revenue</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl p-6 text-white shadow-lg">
                    <p class="text-sm font-medium text-yellow-100">Outstanding</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($outstandingAmount, 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-2xl p-6 text-white shadow-lg">
                    <p class="text-sm font-medium text-red-100">Overdue</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($overdueAmount, 2) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cases with Billing</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">Case</th>
                                <th class="px-4 py-3">Attorney</th>
                                <th class="px-4 py-3">Total Hours</th>
                                <th class="px-4 py-3">Total Expenses</th>
                                <th class="px-4 py-3">Total Billed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($casesWithBilling as $case)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $case->case_number }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $case->assignedAttorney->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ number_format($case->timeEntries->sum('hours'), 1) }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${{ number_format($case->expenses->sum('amount'), 2) }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">${{ number_format($case->invoices->sum('total_amount'), 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No billing data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>