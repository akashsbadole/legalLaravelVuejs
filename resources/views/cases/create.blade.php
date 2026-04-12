<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Case') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Case</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('cases.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                                <input type="text" name="title" id="title" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            </div>
                            <div>
                                <label for="case_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Case Type</label>
                                <input type="text" name="case_type" id="case_type" placeholder="e.g., Civil, Criminal, Family" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                                <select name="status_id" id="status_id" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="assigned_attorney_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned Attorney *</label>
                                <select name="assigned_attorney_id" id="assigned_attorney_id" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                    <option value="">Select Attorney</option>
                                    @foreach(App\Models\User::where('role', 'attorney')->orWhere('role', 'admin')->get() as $attorney)
                                        <option value="{{ $attorney->id }}">{{ $attorney->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="open_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Open Date *</label>
                                <input type="date" name="open_date" id="open_date" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
                            <div>
                                <label for="expected_close_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Close Date</label>
                                <input type="date" name="expected_close_date" id="expected_close_date" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label for="estimated_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Value</label>
                                <input type="number" name="estimated_value" id="estimated_value" step="0.01" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <div class="flex items-center justify-end space-x-3 mt-6">
                            <a href="{{ route('cases.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</a>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 dark:bg-primary-500 text-white hover:bg-primary-700 dark:hover:bg-primary-600 transition">Create Case</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>