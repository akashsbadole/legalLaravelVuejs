<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year, 'view' => $calendarData['view']]) }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $startDate->format('F Y') }}</h3>
                        <a href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year, 'view' => $calendarData['view']]) }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('calendar.index') }}" class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Today</a>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                            <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year, 'view' => 'month']) }}" class="px-3 py-1.5 text-sm {{ $calendarData['view'] === 'month' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">Month</a>
                            <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year, 'view' => 'week']) }}" class="px-3 py-1.5 text-sm {{ $calendarData['view'] === 'week' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">Week</a>
                        </div>
                        <button onclick="document.getElementById('eventModal').showModal()" class="px-3 py-1.5 rounded-lg bg-primary-600 text-white text-sm hover:bg-primary-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Event
                        </button>
                    </div>
                </div>

                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Filter:</span>
                    @php
                    $currentFilters = $calendarData['filters'] ?? [];
                    $filterTypes = ['task', 'hearing', 'deadline', 'meeting', 'milestone'];
                    @endphp
                    @foreach($filterTypes as $type)
                    @php
                        $isActive = in_array($type, $currentFilters);
                        $label = ucfirst($type);
                        if ($type === 'milestone') $label = 'Milestone';
                    @endphp
                    <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year, 'view' => $calendarData['view'], 'filters' => $isActive ? array_diff($currentFilters, [$type]) : array_merge($currentFilters, [$type])]) }}" 
                       class="px-2 py-1 rounded-full text-xs font-medium {{ $isActive ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                    @if(!empty($currentFilters))
                    <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year, 'view' => $calendarData['view']]) }}" class="text-xs text-gray-500 hover:text-primary-600">Clear</a>
                    @endif
                </div>

                <div class="p-4">
                    @if($calendarData['view'] === 'month')
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                <th class="py-2 text-center text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @php
                                $startDay = $calendarData['startDayOfWeek'];
                                $daysInMonth = $calendarData['daysInMonth'];
                                $todayDate = $today->format('Y-m-d');
                                $eventsByDate = $events->groupBy('date');
                                $cellCount = 0;
                                @endphp
                                
                                @for($i = 0; $i < $startDay; $i++)
                                <td class="bg-gray-50 dark:bg-gray-900 min-h-[100px] p-1 border border-gray-200 dark:border-gray-700 align-top"></td>
                                @php $cellCount++; @endphp
                                @endfor
                                
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                    $currentDate = \Carbon\Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                                    $dayEvents = $eventsByDate[$currentDate] ?? collect();
                                    $isToday = $currentDate === $todayDate;
                                    @endphp
                                    <td class="bg-white dark:bg-gray-800 min-h-[100px] p-1 border border-gray-200 dark:border-gray-700 align-top {{ $isToday ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50" onclick="showDayEvents('{{ $currentDate }}')">
                                        <div class="text-sm font-semibold mb-1 {{ $isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $day }}</div>
                                        <div class="space-y-1">
                                            @foreach($dayEvents->take(3) as $event)
                                            @php
                                                $bgClass = 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                                                if (isset($event['milestone']) && $event['milestone']) $bgClass = 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300';
                                                elseif ($event['type'] === 'task') $bgClass = 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300';
                                                elseif ($event['type'] === 'hearing') $bgClass = 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300';
                                                elseif ($event['type'] === 'deadline') $bgClass = 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300';
                                                elseif ($event['type'] === 'meeting') $bgClass = 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
                                            @endphp
                                            <a href="{{ $event['url'] }}" onclick="event.stopPropagation()" class="block text-xs px-1 py-0.5 rounded truncate hover:opacity-80 {{ $bgClass }}">{{ $event['title'] }}</a>
                                            @endforeach
                                            @if($dayEvents->count() > 3)
                                            <span class="text-xs text-gray-500">+{{ $dayEvents->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    @php $cellCount++; @endphp
                                    @if($cellCount % 7 === 0 && $day <= $daysInMonth)
                            </tr>
                            <tr>
                                    @endif
                                @endfor
                                
                                @php
                                $remaining = 7 - ($cellCount % 7);
                                if ($remaining === 7) $remaining = 0;
                                @endphp
                                @for($i = 0; $i < $remaining; $i++)
                                <td class="bg-gray-50 dark:bg-gray-900 min-h-[100px] p-1 border border-gray-200 dark:border-gray-700 align-top"></td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="py-2 text-center text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 w-16">Time</th>
                                @foreach($calendarData['weekDays'] as $day)
                                @php $isToday = $day->format('Y-m-d') === $today->format('Y-m-d'); @endphp
                                <th class="py-2 text-center text-sm font-semibold border-b border-gray-200 dark:border-gray-700 {{ $isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $day->format('D M j') }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for($hour = 0; $hour < 24; $hour++)
                            <tr>
                                <td class="py-1 px-2 text-xs text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700 text-center">{{ sprintf('%02d:00', $hour) }}</td>
                                @foreach($calendarData['weekDays'] as $day)
                                @php
                                $dayDateStr = $day->format('Y-m-d');
                                $hourEvents = $events->filter(fn($e) => $e['date'] === $dayDateStr && substr($e['time'] ?? '00:00', 0, 2) == $hour);
                                @endphp
                                <td class="p-1 border border-gray-100 dark:border-gray-700 align-top min-h-[40px]">
                                    @foreach($hourEvents as $event)
                                    @php
                                        $bgClass = 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                                        if (isset($event['milestone']) && $event['milestone']) $bgClass = 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300';
                                        elseif ($event['type'] === 'task') $bgClass = 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300';
                                        elseif ($event['type'] === 'hearing') $bgClass = 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300';
                                        elseif ($event['type'] === 'deadline') $bgClass = 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300';
                                        elseif ($event['type'] === 'meeting') $bgClass = 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
                                    @endphp
                                    <a href="{{ $event['url'] }}" class="block text-xs px-1 py-0.5 rounded mb-1 truncate {{ $bgClass }}">{{ $event['time'] }} {{ $event['title'] }}</a>
                                    @endforeach
                                </td>
                                @endforeach
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                    @endif
                </div>

                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-indigo-500"></span><span class="text-gray-600 dark:text-gray-400">Milestone</span></div>
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span><span class="text-gray-600 dark:text-gray-400">Task</span></div>
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-gray-600 dark:text-gray-400">Hearing</span></div>
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span><span class="text-gray-600 dark:text-gray-400">Deadline</span></div>
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span class="text-gray-600 dark:text-gray-400">Meeting</span></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Upcoming Tasks</h4>
                    <div class="space-y-2">
                        @php $upcomingTasks = $events->filter(fn($e) => $e['type'] === 'task' && $e['date'] >= $todayDate)->sortBy('date')->take(5); @endphp
                        @forelse($upcomingTasks as $task)
                        <a href="{{ $task['url'] }}" class="block p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100">
                            <div class="text-sm font-medium text-amber-900 dark:text-amber-200">{{ $task['title'] }}</div>
                            <div class="text-xs text-amber-600 dark:text-amber-400">{{ $task['date'] }} @if(isset($task['time'])){{ $task['time'] }}@endif</div>
                        </a>
                        @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming tasks</p>
                        @endforelse
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Upcoming Milestones</h4>
                    <div class="space-y-2">
                        @php $upcomingMilestones = $events->filter(fn($e) => isset($e['milestone']) && $e['milestone'] && $e['date'] >= $todayDate)->sortBy('date')->take(5); @endphp
                        @forelse($upcomingMilestones as $milestone)
                        <a href="{{ $milestone['url'] }}" class="block p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100">
                            <div class="text-sm font-medium text-indigo-900 dark:text-indigo-200">{{ $milestone['title'] }}</div>
                            <div class="text-xs text-indigo-600 dark:text-indigo-400">{{ $milestone['date'] }}</div>
                        </a>
                        @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming milestones</p>
                        @endforelse
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Hearings & Deadlines</h4>
                    <div class="space-y-2">
                        @php $hearings = $events->filter(fn($e) => in_array($e['type'], ['hearing', 'deadline']) && $e['date'] >= $todayDate)->sortBy('date')->take(5); @endphp
                        @forelse($hearings as $hearing)
                        <a href="{{ $hearing['url'] }}" class="block p-2 rounded-lg {{ $hearing['type'] === 'hearing' ? 'bg-red-50 dark:bg-red-900/20' : 'bg-orange-50 dark:bg-orange-900/20' }}">
                            <div class="text-sm font-medium {{ $hearing['type'] === 'hearing' ? 'text-red-900 dark:text-red-200' : 'text-orange-900 dark:text-orange-200' }}">{{ $hearing['title'] }}</div>
                            <div class="text-xs {{ $hearing['type'] === 'hearing' ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' }}">{{ $hearing['date'] }} @if(isset($hearing['time'])){{ $hearing['time'] }}@endif</div>
                        </a>
                        @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <dialog id="eventModal" class="modal p-6 rounded-lg shadow-xl backdrop:bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Event</h3>
                <button onclick="document.getElementById('eventModal').close()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('calendar.store') }}" class="p-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                        <input type="date" name="date" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                        <input type="time" name="time" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select name="type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                        <option value="task">Task</option>
                        <option value="meeting">Meeting</option>
                        <option value="hearing">Hearing</option>
                        <option value="deadline">Deadline</option>
                        <option value="event">Event</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Case (Optional)</label>
                    <select name="case_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                        <option value="">No Case</option>
                        @php
                        $cases = \App\Models\LegalCase::orderBy('case_number')->get();
                        @endphp
                        @foreach($cases as $case)
                        <option value="{{ $case->id }}">{{ $case->case_number }} - {{ $case->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('eventModal').close()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700">Create</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="dayModal" class="modal p-6 rounded-lg shadow-xl backdrop:bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-lg">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="dayModalTitle">Events</h3>
                <button onclick="document.getElementById('dayModal').close()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="dayModalContent" class="p-4 space-y-2 max-h-[400px] overflow-y-auto">
            </div>
        </div>
    </dialog>

    <script>
    function showDayEvents(date) {
        const events = @json($events);
        const dayEvents = events.filter(e => e.date === date);
        const modal = document.getElementById('dayModal');
        const title = document.getElementById('dayModalTitle');
        const content = document.getElementById('dayModalContent');
        
        title.textContent = new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        if (dayEvents.length === 0) {
            content.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No events on this day.</p>';
        } else {
            let html = '';
            dayEvents.forEach(event => {
                let bgClass = 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                if (event.milestone) bgClass = 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300';
                else if (event.type === 'task') bgClass = 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300';
                else if (event.type === 'hearing') bgClass = 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300';
                else if (event.type === 'deadline') bgClass = 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300';
                else if (event.type === 'meeting') bgClass = 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
                
                html += `<a href="${event.url}" class="block p-3 rounded-lg ${bgClass}">
                    <div class="font-medium">${event.title}</div>
                    <div class="text-xs mt-1 opacity-75">${event.time || 'All day'} ${event.case ? '- ' + event.case : ''}</div>
                </a>`;
            });
            content.innerHTML = html;
        }
        
        modal.showModal();
    }
    </script>
</x-app-layout>