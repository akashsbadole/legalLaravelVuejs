<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Onboarding Progress') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Onboarding Status</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $progress['completed'] }} of {{ $progress['total'] }} steps completed ({{ $progress['percentage'] }}%)</p>
                </div>
                <div class="p-6">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-6">
                        <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $progress['percentage'] }}%"></div>
                    </div>

                    <div class="space-y-4">
                        @foreach($progress['steps'] as $stepKey => $stepName)
                            <div class="flex items-center space-x-3 p-4 rounded-lg {{ in_array($stepKey, $progress['completed_steps']) ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }}">
                                <div class="flex-shrink-0">
                                    @if(in_array($stepKey, $progress['completed_steps']))
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-500 rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $stepName }}</p>
                                    @if(in_array($stepKey, $progress['completed_steps']))
                                        <p class="text-xs text-green-600 dark:text-green-400">Completed</p>
                                    @else
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                    @endif
                                </div>
                                @if(!in_array($stepKey, $progress['completed_steps']))
                                    <div class="flex-shrink-0">
                                        @if($stepKey === 'document_collection')
                                            <a href="{{ route('portal.documents') }}" class="text-primary-600 hover:text-primary-700 text-sm">Upload Documents</a>
                                        @elseif($stepKey === 'kyc_verification')
                                            <span class="text-gray-400 text-sm">Contact Support</span>
                                        @elseif($stepKey === 'engagement_letter')
                                            <span class="text-gray-400 text-sm">Will be sent via email</span>
                                        @elseif($stepKey === 'initial_consultation')
                                            <span class="text-gray-400 text-sm">Scheduled by attorney</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($progress['percentage'] < 100)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Next Steps</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p>Complete the remaining steps to fully activate your account. If you need assistance, contact our support team.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>