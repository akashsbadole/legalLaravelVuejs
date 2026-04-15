<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audit Log Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white darnk:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Log Details</h1>
                            <p class="text-gray-600 dark:text-gray-400">Detailed information about this audit log entry</p>
                        </div>
                        <a href="{{ route('admin.audit-logs.index') }}"
                           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            ← Back to Logs
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Log Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Log Information</h3>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Log ID:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">#{{ $auditLog->id }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Timestamp:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $auditLog->created_at->format('M j, Y \a\t g:i:s A') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Action:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if(str_contains($auditLog->action, 'create'))
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif(str_contains($auditLog->action, 'update'))
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif(str_contains($auditLog->action, 'delete'))
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif(str_contains($auditLog->action, 'login'))
                                                bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @else
                                                bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $auditLog->action)) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $auditLog->ip_address ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- User Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Information</h3>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    @if($auditLog->user)
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                                <span class="text-sm text-primary-600 dark:text-primary-400 font-semibold">
                                                    {{ substr($auditLog->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $auditLog->user->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auditLog->user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-500 dark:text-gray-400">Role:</span>
                                                <span class="text-gray-900 dark:text-white capitalize">{{ $auditLog->user->role }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                                                <span class="text-gray-900 dark:text-white">{{ $auditLog->user->phone ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500 dark:text-gray-400">Member Since:</span>
                                                <span class="text-gray-900 dark:text-white">{{ $auditLog->user->created_at->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">System action (no user associated)</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description & Metadata -->
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    @if($auditLog->description)
                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $auditLog->description }}</p>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No description provided</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Metadata -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Metadata</h3>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    @if($auditLog->metadata)
                                        <pre class="text-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800 p-3 rounded border overflow-x-auto">{{ json_encode(json_decode($auditLog->metadata), JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No metadata available</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity by User</h3>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 max-h-64 overflow-y-auto">
                                    @if($auditLog->user)
                                        @php
                                            $recentLogs = \App\Models\AuditLog::where('user_id', $auditLog->user_id)
                                                ->where('id', '!=', $auditLog->id)
                                                ->orderBy('created_at', 'desc')
                                                ->limit(5)
                                                ->get();
                                        @endphp

                                        @if($recentLogs->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($recentLogs as $log)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                                @if(str_contains($log->action, 'create')) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                                @elseif(str_contains($log->action, 'update')) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                                @elseif(str_contains($log->action, 'delete')) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                                {{ ucfirst($log->action) }}
                                                            </span>
                                                            <span class="text-gray-600 dark:text-gray-400 truncate max-w-xs">
                                                                {{ Str::limit($log->description ?? 'No description', 30) }}
                                                            </span>
                                                        </div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $log->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No other recent activity</p>
                                        @endif
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">System actions have no user activity history</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
