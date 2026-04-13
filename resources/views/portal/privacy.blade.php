<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Privacy Settings') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data Protection Rights (PDPB Compliance)</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Under India's Personal Data Protection Bill, you have the right to access, correct, and delete your personal data.
                    </p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Your Consents</h4>
                            @if($consents->count() > 0)
                                <div class="mt-2 space-y-2">
                                    @foreach($consents as $consent)
                                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                            <div class="flex justify-between">
                                                <span class="text-gray-900 dark:text-white">{{ ucfirst($consent->purpose) }}</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $consent->action }} on {{ $consent->granted_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No consents recorded yet.</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('portal.privacy.export') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Export My Data
                                </button>
                            </form>

                            <form method="POST" action="{{ route('portal.privacy.delete') }}" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete your data? This action cannot be undone.')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    Delete My Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Consent Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Manage your consent for data processing and communications.
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('portal.privacy.consent') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="marketing_consent" value="1" {{ $clientProfile && $clientProfile->marketing_consent ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700 dark:text-gray-300">I consent to receiving marketing communications</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="data_processing_consent" value="1" {{ $clientProfile && $clientProfile->data_processing_consent ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700 dark:text-gray-300">I consent to my data being processed for service provision</span>
                            </label>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Update Consents
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>