<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Generate Document') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Generate Document from Template</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('documents.generate.store') }}">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label for="template_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Template *</label>
                                <select name="template_id" id="template_id" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                    <option value="">Select Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="case_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Case (Optional)</label>
                                <select name="case_id" id="case_id" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">No Case</option>
                                    @foreach($cases as $case)
                                        <option value="{{ $case->id }}">{{ $case->case_number }} - {{ $case->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Generate Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>