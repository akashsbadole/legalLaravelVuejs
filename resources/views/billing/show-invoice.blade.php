<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Invoice #') . $invoice->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Invoice Details</h3>
                    @if($invoice->status == 'paid')
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Paid</span>
                    @elseif($invoice->status == 'overdue')
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Overdue</span>
                    @else
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">Pending</span>
                    @endif
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Client</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $invoice->client->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Case</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $invoice->case?->case_number ?? 'N/A' }}</p>
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
                        @if($invoice->gstin)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Client GSTIN</p>
                            <p class="text-gray-900 dark:text-white">{{ $invoice->gstin }}</p>
                        </div>
                        @endif
                        @if($invoice->sac_code)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">SAC Code</p>
                            <p class="text-gray-900 dark:text-white">{{ $invoice->sac_code }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Line Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Qty</th>
                                <th class="px-4 py-3">Unit Price</th>
                                <th class="px-4 py-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($invoice->lineItems as $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $item->type }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">₹{{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No line items.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-medium">Subtotal</td>
                                <td class="px-4 py-3">₹{{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @if($invoice->cgst_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right">CGST (9%)</td>
                                <td class="px-4 py-2">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->sgst_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right">SGST (9%)</td>
                                <td class="px-4 py-2">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->igst_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right">IGST (18%)</td>
                                <td class="px-4 py-2">₹{{ number_format($invoice->igst_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->tax_amount > 0 && !$invoice->cgst_amount && !$invoice->sgst_amount && !$invoice->igst_amount)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right">GST (18%)</td>
                                <td class="px-4 py-2">₹{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right">Discount</td>
                                <td class="px-4 py-2">₹{{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                                <td colspan="4" class="px-4 py-3 text-right font-bold text-lg">Total</td>
                                <td class="px-4 py-3 font-bold text-lg">₹{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                @if($invoice->status == 'draft')
                <form method="POST" action="{{ route('billing.invoices.send', $invoice) }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-green-600 dark:bg-green-500 text-white hover:bg-green-700 dark:hover:bg-green-600 transition">Mark as Sent</button>
                </form>
                @endif
                @if($invoice->status == 'sent')
                <form method="POST" action="{{ route('billing.invoices.paid', $invoice) }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Mark as Paid</button>
                </form>
                @endif
                <a href="{{ route('billing.invoices') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </div>
</x-app-layout>