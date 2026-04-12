<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Settings') }}
            </h2>
            <a href="{{ route('admin.settings.create') }}" class="btn-primary px-4 py-2 rounded">
                {{ __('New Setting') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:mb-1">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4">
                    <div class="space-y-6">
                        @foreach($settings as $group => $groupSettings)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ ucfirst($group) }}</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($groupSettings as $key => $value)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                                            <div class="flex items-center mb-2">
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                                    {{ $key }}
                                                </span>
                                                @if(auth()->user()->isAdmin())
                                                    <a href="{{ route('admin.settings.edit', \App\Models\Setting::where('key', $key)->where('group', $group)->first()->id) }}" 
                                                       class="ml-auto text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                        Edit
                                                    </a>
                                                @endif
                                            </div>
                                            
                                            <div class="text-gray-700 dark:text-gray-300">
                                                @if(is_bool($value))
                                                    <span class="font-medium">{{ $value ? 'Yes' : 'No' }}</span>
                                                @elseif(is_array($value) || is_object($value))
                                                    <pre class="text-xs bg-gray-50 dark:bg-gray-700 p-2 rounded">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    <span class="break-all">{{ $value }}</span>
                                                @endif
                                            </div>
                                            
@if(!empty(\App\Models\Setting::where('key', $key)->where('group', $group)->first()->description))
                                                 <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">
                                                     {{ \App\Models\Setting::where('key', $key)->where('group', $group)->first()->description }}
                                                 </p>
                                             @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>