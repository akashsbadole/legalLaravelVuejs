<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Document Templates') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <div class="text-sm text-gray-600 dark:text-gray-400">Manage your document templates</div>
                <a href="{{ route('documents.templates.create') }}" class="px-4 py-2 bg-primary-600 dark:bg-primary-500 text-white rounded-lg hover:bg-primary-700 dark:hover:bg-primary-600">
                    + New Template
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($templates as $template)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 hover:shadow-md transition">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $template->name }}</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($template->description, 60) }}</p>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="mr-3">Category: {{ $template->category->name ?? 'None' }}</span>
                        <span>Version: {{ $template->version }}</span>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('documents.templates.preview', $template) }}" class="px-3 py-1 text-sm border border-primary-600 dark:border-primary-400 text-primary-600 dark:text-primary-400 rounded hover:bg-primary-50 dark:hover:bg-primary-900">Preview</a>
                        <a href="{{ route('documents.templates.edit', $template) }}" class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Edit</a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">No templates yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>