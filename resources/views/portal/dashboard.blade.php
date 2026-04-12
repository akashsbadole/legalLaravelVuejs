<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-primary-500">
                    <h5 class="text-sm text-gray-500 dark:text-gray-400">My Cases</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $cases->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-accent-500">
                    <h5 class="text-sm text-gray-500 dark:text-gray-400">My Documents</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $documents->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-yellow-500">
                    <h5 class="text-sm text-gray-500 dark:text-gray-400">Pending Invoices</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $invoices->count() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Cases</h3>
                    </div>
                    <div class="p-4">
                        @forelse($cases as $case)
                        <div class="mb-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <a href="{{ route('portal.cases.show', $case) }}" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">{{ $case->case_number }}</a>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $case->title }}</p>
                        </div>
                        @empty
                        <p class="text-gray-500 dark:text-gray-400">No cases yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Documents</h3>
                    </div>
                    <div class="p-4">
                        @forelse($documents as $doc)
                        <div class="mb-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                            <span class="text-gray-700 dark:text-gray-200">{{ Str::limit($doc->name, 25) }}</span>
                            <a href="{{ route('portal.documents.download', $doc) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Download</a>
                        </div>
                        @empty
                        <p class="text-gray-500 dark:text-gray-400">No documents yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">My Invoices</h3>
                @forelse($invoices as $invoice)
                <div class="mb-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</span>
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">${{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($invoice->status == 'paid')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Paid</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300">Pending</span>
                        @endif
                        <a href="{{ route('portal.invoices.show', $invoice) }}" class="text-primary-600 dark:text-primary-400 hover:underline">View</a>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 dark:text-gray-400">No invoices yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>