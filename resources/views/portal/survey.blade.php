<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Feedback Survey') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $survey->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $survey->description }}</p>
                </div>
                <form method="POST" action="{{ route('portal.survey.submit', $survey) }}" class="p-6">
                    @csrf

                    @foreach($survey->questions as $index => $question)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ $index + 1 }}. {{ $question['question'] }}
                            </label>

                            @if($question['type'] === 'rating')
                                <div class="flex space-x-2">
                                    @foreach($question['options'] as $option)
                                        <label class="flex items-center">
                                            <input type="radio" name="responses[{{ $index }}]" value="{{ $option }}" class="mr-1" required>
                                            <span class="text-sm">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($question['type'] === 'text')
                                <textarea name="responses[{{ $index }}]" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Your answer..."></textarea>
                            @elseif($question['type'] === 'yes_no')
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="responses[{{ $index }}]" value="yes" class="mr-1" required>
                                        <span class="text-sm">Yes</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="responses[{{ $index }}]" value="no" class="mr-1" required>
                                        <span class="text-sm">No</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="mb-6">
                        <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Additional Feedback (Optional)
                        </label>
                        <textarea name="feedback" id="feedback" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Any additional comments or suggestions..."></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('portal.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Skip</a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Submit Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>