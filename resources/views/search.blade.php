<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search Results') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" class="mb-6">
                <div class="flex gap-4">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search cases, tasks, documents, clients..." 
                        class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-lg">
                    <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">Search</button>
                </div>
            </form>

            @if(request('q'))
            <div class="space-y-6">
                @php
                $query = request('q');
                
                $cases = \App\Models\LegalCase::where('title', 'like', "%{$query}%")
                    ->orWhere('case_number', 'like', "%{$query}%")->take(5)->get();
                    
                $tasks = \App\Models\CaseTask::where('title', 'like', "%{$query}%")->take(5)->get();
                
                $clients = \App\Models\User::where('role', 'client')
                    ->where(function($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })->take(5)->get();
                    
                $documents = \App\Models\Document::where('name', 'like', "%{$query}%")->take(5)->get();
                @endphp

                @if($cases->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Cases ({{ $cases->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($cases as $case)
                        <a href="{{ route('cases.show', $case) }}" class="block px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $case->case_number }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $case->title }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">{{ $case->status?->name ?? 'N/A' }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($tasks->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Tasks ({{ $tasks->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($tasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $task->case?->case_number ?? 'No case' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($task->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                    @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 @endif">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($clients->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Clients ({{ $clients->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($clients as $client)
                        <div class="px-6 py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client->email }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($documents->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Documents ({{ $documents->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($documents as $doc)
                        <a href="{{ route('documents.show', $doc) }}" class="block px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $doc->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $doc->category?->name ?? 'Uncategorized' }}</p>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($cases->count() === 0 && $tasks->count() === 0 && $clients->count() === 0 && $documents->count() === 0)
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">No results found for "{{ request('q') }}"</p>
                </div>
                @endif
            </div>
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Enter a search term to find cases, tasks, clients, and documents</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>