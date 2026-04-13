<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
            <a href="{{ route('cases.index') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.total_cases') }}</p>
                        <p class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\LegalCase::count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-green-600 dark:text-green-400">{{ \App\Models\LegalCase::whereHas('status', fn($q) => $q->where('name', 'Open'))->count() }}</span> open
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('tasks.my') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-accent-200 dark:hover:border-accent-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">My Tasks</p>
                        <p class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\CaseTask::where('assigned_to', auth()->id())->where('status', '!=', 'completed')->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-red-600 dark:text-red-400">{{ \App\Models\CaseTask::where('assigned_to', auth()->id())->where('priority', 'urgent')->where('status', '!=', 'completed')->count() }}</span> urgent
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-gradient-to-br from-accent-400 to-accent-600 flex items-center justify-center shadow-lg shadow-accent-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('billing.invoices') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-yellow-200 dark:hover:border-yellow-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Invoices</p>
                        <p class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Invoice::whereIn('status', ['sent', 'viewed', 'overdue'])->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-yellow-600 dark:text-yellow-400">${{ number_format(\App\Models\Invoice::whereIn('status', ['sent', 'viewed'])->sum('total_amount'), 0) }}</span> pending
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg shadow-yellow-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.financials') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-green-200 dark:hover:border-green-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2">${{ number_format(\App\Models\Invoice::where('status', 'paid')->sum('total_amount'), 0) }}</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">
                            {{ \App\Models\Invoice::where('status', 'paid')->count() }} paid invoices
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 lg:px-6 lg:py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Cases</h3>
                    </div>
                    <a href="{{ route('cases.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">View all</a>
                </div>
                <div class="p-4 lg:p-5">
                    <div class="space-y-3">
                        @forelse(\App\Models\LegalCase::with('status')->latest()->take(5)->get() as $case)
                        <a href="{{ route('cases.show', $case) }}" class="flex items-center justify-between p-3 lg:p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                            <div class="flex items-center space-x-3 lg:space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/20 flex items-center justify-center">
                                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ substr($case->case_number, -2) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $case->case_number }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($case->title, 28) }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">{{ $case->status->name ?? 'N/A' }}</span>
                        </a>
                        @empty
                        <div class="text-center py-8"><p class="text-gray-500 dark:text-gray-400">No cases yet</p></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 lg:px-6 lg:py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-400 to-accent-600 flex items-center justify-center shadow-lg shadow-accent-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Tasks</h3>
                    </div>
                    <a href="{{ route('tasks.my') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">View all</a>
                </div>
                <div class="p-4 lg:p-5">
                    <div class="space-y-3">
                        @php
                        $upcomingTasks = \App\Models\CaseTask::with('case')
                            ->where('assigned_to', auth()->id())
                            ->where('status', '!=', 'completed')
                            ->whereNotNull('due_date')
                            ->orderBy('due_date')
                            ->take(5)
                            ->get();
                        @endphp
                        @forelse($upcomingTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="flex items-center justify-between p-3 lg:p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full {{ $task->priority === 'urgent' ? 'bg-red-500' : ($task->priority === 'high' ? 'bg-orange-500' : 'bg-blue-500') }}"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($task->title, 25) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $task->case?->case_number }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $task->due_date?->format('M d') }}</span>
                        </a>
                        @empty
                        <div class="text-center py-8"><p class="text-gray-500 dark:text-gray-400">No upcoming tasks</p></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 lg:px-6 lg:py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Leads</h3>
                    </div>
                    <a href="{{ route('leads.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">View all</a>
                </div>
                <div class="p-4 lg:p-5">
                    <div class="space-y-3">
                        @php
                        $recentLeads = \App\Models\Lead::orderBy('created_at', 'desc')->take(5)->get();
                        @endphp
                        @forelse($recentLeads as $lead)
                        <a href="{{ route('leads.show', $lead) }}" class="flex items-center justify-between p-3 lg:p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lead->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->email }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($lead->status === 'new') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($lead->status === 'contacted') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($lead->status === 'qualified') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </a>
                        @empty
                        <div class="text-center py-8"><p class="text-gray-500 dark:text-gray-400">No leads yet</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-5 lg:p-6 text-white shadow-lg">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <h3 class="text-lg font-semibold">Quick Actions</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('cases.create') }}" class="flex items-center p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                        <span class="font-medium">Create New Case</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('tasks.create') }}" class="flex items-center p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                        <span class="font-medium">Assign New Task</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('documents.generate') }}" class="flex items-center p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                        <span class="font-medium">Generate Document</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('calendar.index') }}" class="flex items-center p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                        <span class="font-medium">View Calendar</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 lg:px-6 lg:py-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg shadow-green-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue (Last 6 Months)</h3>
                    </div>
                </div>
                <div class="p-4 lg:p-5">
                    @php
                    $revenueData = [];
                    $months = [];
                    for ($i = 5; $i >= 0; $i--) {
                        $month = now()->subMonths($i);
                        $months[] = $month->format('M');
                        $revenueData[] = \App\Models\Invoice::where('status', 'paid')
                            ->whereYear('paid_date', $month->year)
                            ->whereMonth('paid_date', $month->month)
                            ->sum('total_amount');
                    }
                    @endphp
                    <canvas id="revenueChart" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 lg:px-6 lg:py-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg shadow-yellow-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Case Status Distribution</h3>
                    </div>
                </div>
                <div class="p-4 lg:p-5">
                    @php
                    $statusCounts = \App\Models\CaseStatus::withCount('cases')->get();
                    @endphp
                    <canvas id="caseStatusChart" height="180"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 lg:px-6 lg:py-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Calendar - This Month</h3>
                    </div>
                </div>
                <div class="p-4 lg:p-5">
                    @php
                    $calendarEvents = \App\Models\CaseTimelineEvent::whereMonth('event_date', now()->month)
                        ->whereYear('event_date', now()->year)
                        ->orderBy('event_date')
                        ->take(6)
                        ->get();
                    @endphp
                    <div class="space-y-3">
                        @forelse($calendarEvents as $event)
                        <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mr-3">
                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $event->event_date->format('d') }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($event->title, 30) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->case?->case_number }} - {{ ucfirst($event->event_type) }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8"><p class="text-gray-500 dark:text-gray-400">No events this month</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueData),
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
                    x: { grid: { display: false } }
                }
            }
        });

        const ctx2 = document.getElementById('caseStatusChart').getContext('2d');
        const statusData = @json($statusCounts->map(fn($s) => $s->cases_count));
        const statusLabels = @json($statusCounts->map(fn($s) => $s->name));
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'];
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: colors.slice(0, statusLabels.length),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
    @endpush
</x-app-layout>