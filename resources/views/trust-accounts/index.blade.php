<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Trust Accounts') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
@php
$totalBalance = \App\Models\TrustAccount::select('client_id')
    ->groupBy('client_id')
    ->selectRaw('SUM(CASE WHEN transaction_type IN (\'deposit\', \'adjustment\') THEN amount ELSE -amount END) as balance')
    ->get()->sum('balance');
$totalDeposits = \App\Models\TrustAccount::where('transaction_type', 'deposit')->sum('amount');
$totalWithdrawals = \App\Models\TrustAccount::whereIn('transaction_type', ['withdrawal', 'transfer_to_operating'])->sum('amount');
@endphp
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Trust Balance</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">${{ number_format($totalBalance, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Deposits</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">${{ number_format($totalDeposits, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Withdrawals</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">${{ number_format($totalWithdrawals, 2) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transactions</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('trust-accounts.create') }}" class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700">+ New Transaction</a>
                    </div>
                </div>
                
                <form method="GET" class="p-4 flex flex-wrap gap-4 border-b border-gray-200 dark:border-gray-700">
                    <select name="client" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Filter</button>
                    <a href="{{ route('trust-accounts.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50">Reset</a>
                </form>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Reference</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Client</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Case</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Type</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Balance</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($accounts as $account)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('trust-accounts.show', $account) }}" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">
                                        {{ $account->reference_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $account->client?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $account->case?->case_number ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ ucfirst($account->transaction_type) }}</td>
                                <td class="px-4 py-3 {{ $account->is_debit ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} font-medium">
                                    {{ $account->is_debit ? '-' : '+' }}${{ number_format($account->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">${{ number_format($account->balance, 2) }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $account->transaction_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($account->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($account->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                                        {{ ucfirst($account->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No transactions found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($accounts->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $accounts->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>