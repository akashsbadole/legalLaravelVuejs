<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('CRM Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Leads</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ \App\Models\Lead::count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ \App\Models\Lead::where('created_at', '>=', now()->subDays(30))->count() }} this month</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Qualified Leads</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ \App\Models\Lead::where('status', 'qualified')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ round(\App\Models\Lead::where('status', 'qualified')->count() / max(\App\Models\Lead::count(), 1) * 100) }}% conversion</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Won Deals</p>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ \App\Models\Lead::where('status', 'won')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Converted leads</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Conversion Rate</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                                {{ round(\App\Models\Lead::whereIn('status', ['won', 'qualified'])->count() / max(\App\Models\Lead::count(), 1) * 100) }}%
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Qualified + Won / Total</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Pipeline Overview</h3>
                    </div>
                    <div class="p-5">
                        @php
                        $pipeline = [
                            'new' => \App\Models\Lead::where('status', 'new')->count(),
                            'contacted' => \App\Models\Lead::where('status', 'contacted')->count(),
                            'qualified' => \App\Models\Lead::where('status', 'qualified')->count(),
                            'proposal' => \App\Models\Lead::where('status', 'proposal')->count(),
                            'won' => \App\Models\Lead::where('status', 'won')->count(),
                        ];
                        $total = array_sum($pipeline);
                        @endphp
                        <div class="space-y-4">
                            @foreach(['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'proposal' => 'Proposal', 'won' => 'Won'] as $key => $label)
                            @php $count = $pipeline[$key]; $percent = $total > 0 ? ($count / $total * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $key === 'new' ? 'bg-blue-500' : ($key === 'contacted' ? 'bg-yellow-500' : ($key === 'qualified' ? 'bg-green-500' : ($key === 'proposal' ? 'bg-purple-500' : 'bg-emerald-500'))) }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Recent Leads</h3>
                        <a href="{{ route('leads.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">View All</a>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(\App\Models\Lead::latest()->take(5)->get() as $lead)
                        <a href="{{ route('leads.show', $lead) }}" class="block px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $lead->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $lead->company ?? $lead->email }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($lead->status === 'new') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($lead->status === 'contacted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($lead->status === 'qualified') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">No leads yet</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Lead Sources</h3>
                    </div>
                    <div class="p-5">
                        @php
                        $sources = \App\Models\Lead::select('source')->groupBy('source')->get()->mapWithKeys(function($l) { return [$l->source => \App\Models\Lead::where('source', $l->source)->count()]; });
                        @endphp
                        <div class="space-y-3">
                            @forelse($sources as $source => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($source ?? 'Unknown') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $count }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No source data</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden lg:col-span-2">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Top Performers</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">User</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Leads</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Won</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 dark:text-gray-400">Conversion</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                $users = \App\Models\User::whereHas('leads')->withCount('leads')->get()->map(function($u) {
                                    $won = \App\Models\Lead::where('assigned_to', $u->id)->where('status', 'won')->count();
                                    return ['user' => $u, 'leads' => $u->leads_count, 'won' => $won, 'rate' => $u->leads_count > 0 ? round($won / $u->leads_count * 100) : 0];
                                })->sortByDesc('won')->take(5);
                                @endphp
                                @forelse($users as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $item['user']->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ $item['leads'] }}</td>
                                    <td class="px-4 py-2 text-sm text-green-600 dark:text-green-400">{{ $item['won'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ $item['rate'] }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>