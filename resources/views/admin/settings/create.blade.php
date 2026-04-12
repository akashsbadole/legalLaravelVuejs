<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('New Setting') }}
            </h2>
            <a href="{{ route('admin.settings.index') }}" class="btn-secondary">
                {{ __('Back to Settings') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong>{{ __('Whoops!') }}</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form action="{{ route('admin.settings.store') }}" method="POST" class="px-6 py-6">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <label for="key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Key') }}
                            </label>
                            <input type="text" name="key" id="key" value="{{ old('key') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                   placeholder="{{ __('Enter setting key (e.g., site_name)) }}" required>
                        </div>

                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Value') }}
                            </label>
                            <textarea name="value" id="value" rows="4" 
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                      placeholder="{{ __('Enter setting value') }}">{{ old('value') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Group') }}
                                </label>
                                <select name="group" id="group" 
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                                    <option value="general" {{ old('group') == 'general' ? 'selected' : '' }}>{{ __('General') }}</option>
                                    <option value="email" {{ old('group') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                                    <option value="upload" {{ old('group') == 'upload' ? 'selected' : '' }}>{{ __('Upload') }}</option>
                                    <option value="language" {{ old('group') == 'language' ? 'selected' : '' }}>{{ __('Language') }}</option>
                                    <option value="date" {{ old('group') == 'date' ? 'selected' : '' }}>{{ __('Date & Time') }}</option>
                                    <option value="security" {{ old('group') == 'security' ? 'selected' : '' }}>{{ __('Security') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Description') }}
                                </label>
                                <input type="text" name="description" id="description" value="{{ old('description') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                       placeholder="{{ __('Enter description (optional)') }}">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="btn-primary">
                            {{ __('Create Setting') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>