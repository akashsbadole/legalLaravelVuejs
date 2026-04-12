<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Export Data') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Cases</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Export all case data including status, attorney, and dates.</p>
                    <a href="{{ route('export.cases') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Export CSV</a>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Invoices</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Export all invoice data with amounts and payment status.</p>
                    <a href="{{ route('export.invoices') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Export CSV</a>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Time Entries</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Export time entries with hours, rates, and billing status.</p>
                    <a href="{{ route('export.time-entries') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Export CSV</a>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Expenses</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Export expenses with categories and billable status.</p>
                    <a href="{{ route('export.expenses') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Export CSV</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>