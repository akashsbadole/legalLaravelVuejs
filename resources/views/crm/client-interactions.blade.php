<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Client Profile: :name', ['name' => $client->name]) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Client Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Overview</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Information</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->email }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->phone }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->address_line1 }}, {{ $client->city }}, {{ $client->state }} {{ $client->postal_code }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Client Details</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">Type: {{ ucfirst($client->client_type) }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">Priority: {{ ucfirst($client->priority) }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">Status: {{ ucfirst($client->status) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Financial Summary</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">Total Invoiced: ₹{{ number_format($totalInvoiced, 2) }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">Total Paid: ₹{{ number_format($totalPaid, 2) }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">Outstanding: ₹{{ number_format($outstanding, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Related Cases -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Related Cases</h3>
                    </div>
                    <div class="p-6">
                        @forelse($cases as $case)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $case->title }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Case #{{ $case->case_number }} - {{ $case->status->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Attorney: {{ $case->assignedAttorney->name }}</p>
                                <a href="{{ route('cases.show', $case) }}" class="text-primary-600 hover:text-primary-700 text-sm">View Case</a>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No cases found.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Documents -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Documents</h3>
                    </div>
                    <div class="p-6">
                        @forelse($documents as $document)
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $document->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $document->created_at->format('M d, Y') }}</p>
                                </div>
                                <a href="{{ route('documents.download', $document) }}" class="text-primary-600 hover:text-primary-700 text-sm">Download</a>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No documents found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Recent Invoices -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Invoices</h3>
                    </div>
                    <div class="p-6">
                        @forelse($invoices as $invoice)
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Invoice #{{ $invoice->invoice_number }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">₹{{ number_format($invoice->total_amount, 2) }} - {{ $invoice->status }}</p>
                                </div>
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-700 text-sm">View</a>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No invoices found.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Communication History -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Communication History</h3>
                        <a href="{{ route('crm.interactions.create', $client) }}" class="text-primary-600 hover:text-primary-700 text-sm">Log Interaction</a>
                    </div>
                    <div class="p-6">
                        @forelse($interactions as $interaction)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ ucfirst($interaction->type) }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $interaction->subject }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $interaction->sent_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $interaction->status === 'sent' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $interaction->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No interactions found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Follow-ups -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scheduled Follow-ups</h3>
                    <a href="{{ route('crm.follow-ups.create', $client) }}" class="text-primary-600 hover:text-primary-700 text-sm">Schedule Follow-up</a>
                </div>
                <div class="p-6">
                    @forelse($followUps as $followUp)
                        <div class="mb-4 flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $followUp->subject }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ ucfirst($followUp->type) }} - Scheduled for {{ $followUp->scheduled_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $followUp->status === 'pending' ? 'bg-blue-100 text-blue-800' : ($followUp->status === 'sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ $followUp->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">No follow-ups scheduled.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>