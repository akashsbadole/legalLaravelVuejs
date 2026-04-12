<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Invoice Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Invoice #{{ $invoice->invoice_number }}</span>
                    </div>
                    @if($invoice->status == 'paid')
                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Paid</span>
                    @elseif($invoice->status == 'overdue')
                    <span class="px-3 py-1 text-sm rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">Overdue</span>
                    @else
                    <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300">Pending</span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Case</p>
                            <p class="text-gray-900 dark:text-white">{{ $invoice->case->case_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Issue Date</p>
                            <p class="text-gray-900 dark:text-white">{{ $invoice->issue_date?->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
                            <p class="text-gray-900 dark:text-white">{{ $invoice->due_date?->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Paid Date</p>
                            <p class="text-gray-900 dark:text-white">{{ $invoice->paid_date?->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Line Items</h3>
                </div>
                <div class="p-5">
                    <table class="w-full text-sm">
                        <thead class="text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-2 text-left">Description</th>
                                <th class="px-3 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->lineItems as $item)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-300">{{ $item->description }}</td>
                                <td class="px-3 py-3 text-gray-900 dark:text-white text-right">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">No items.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="text-gray-900 dark:text-white">
                            <tr>
                                <td class="px-3 py-2 text-right font-medium">Subtotal:</td>
                                <td class="px-3 py-2 text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @if($invoice->tax_amount > 0)
                            <tr>
                                <td class="px-3 py-2 text-right font-medium">Tax:</td>
                                <td class="px-3 py-2 text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                                <td class="px-3 py-2 text-right font-bold text-lg">Total:</td>
                                <td class="px-3 py-2 text-right font-bold text-lg">${{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($invoice->status != 'paid')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pay Invoice</h3>
                </div>
                <div class="p-5">
                    @if(config('services.stripe.secret_key'))
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                        <input type="text" id="card-number" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="4242 4242 4242 4242">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry</label>
                            <input type="text" id="card-expiry" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="MM/YY">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVC</label>
                            <input type="text" id="card-cvc" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="123">
                        </div>
                    </div>
                    <button id="pay-button" onclick="processPayment()" class="w-full px-4 py-3 bg-primary-600 dark:bg-primary-500 text-white rounded-lg hover:bg-primary-700 dark:hover:bg-primary-600 font-medium">
                        Pay ${{ number_format($invoice->total_amount, 2) }}
                    </button>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Secure payment powered by Stripe</p>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">This is a demo payment. Click below to simulate payment.</p>
                        <form method="POST" action="{{ route('portal.invoices.pay', $invoice) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-accent-600 dark:bg-accent-500 text-white rounded-lg hover:bg-accent-700 dark:hover:bg-accent-600 font-medium">
                                Simulate Payment - ${{ number_format($invoice->total_amount, 2) }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="bg-green-50 dark:bg-green-900/30 rounded-lg shadow mb-4 p-5 text-center">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-700 dark:text-green-300 font-medium">This invoice has been paid</p>
                <p class="text-sm text-green-600 dark:text-green-400">Paid on {{ $invoice->paid_date?->format('M d, Y') }}</p>
            </div>
            @endif

            <a href="{{ route('portal.invoices') }}" class="inline-block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                Back to Invoices
            </a>
        </div>
    </div>
</x-app-layout>