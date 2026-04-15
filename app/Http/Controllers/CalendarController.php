<?php

namespace App\Http\Controllers;

use App\Models\CaseTask;
use App\Models\CaseTimelineEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $view = $request->get('view', 'month');
        $filters = $request->get('filters', []);

        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $today = Carbon::today();

        $tasks = CaseTask::whereBetween('due_date', [$startDate, $endDate])
            ->whereNotNull('due_date')
            ->with(['case', 'assignedTo'])
            ->get();

        $timelineEvents = CaseTimelineEvent::whereBetween('event_date', [$startDate, $endDate])
            ->whereNotNull('event_date')
            ->with(['case', 'user'])
            ->get();

        $events = collect();

        foreach ($tasks as $task) {
            $events->push([
                'id' => 'task-'.$task->id,
                'title' => $task->title,
                'date' => $task->due_date->format('Y-m-d'),
                'time' => $task->due_date->format('H:i'),
                'type' => 'task',
                'priority' => $task->priority,
                'status' => $task->status,
                'case' => $task->case?->case_number,
                'case_id' => $task->case?->id,
                'assigned_to' => $task->assignedTo?->name,
                'url' => route('tasks.show', $task),
            ]);
        }

        foreach ($timelineEvents as $event) {
            $events->push([
                'id' => 'event-'.$event->id,
                'title' => $event->title,
                'date' => $event->event_date->format('Y-m-d'),
                'time' => $event->event_date->format('H:i'),
                'type' => $event->event_type,
                'milestone' => $event->is_milestone,
                'case' => $event->case?->case_number,
                'case_id' => $event->case?->id,
                'url' => $event->case ? route('cases.show', $event->case) : '#',
            ]);
        }

        if (! empty($filters) && is_array($filters)) {
            $events = $events->filter(fn ($e) => in_array($e['type'], $filters));
        }

        $daysInMonth = $startDate->daysInMonth;
        $startDayOfWeek = $startDate->dayOfWeek;

        $weekStart = $startDate->copy()->startOfWeek();
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = $weekStart->copy()->addDays($i);
        }

        $prevMonth = $startDate->copy()->subMonth();
        $nextMonth = $startDate->copy()->addMonth();

        $calendarData = [
            'year' => $year,
            'month' => $month,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'view' => $view,
            'filters' => $filters,
            'weekDays' => $weekDays,
            'weekStart' => $weekStart,
        ];

        return view('calendar.index', compact('events', 'startDate', 'endDate', 'month', 'year', 'prevMonth', 'nextMonth', 'today', 'calendarData'));
    }

    public function getEvents(Request $request)
    {
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));

        $tasks = CaseTask::whereBetween('due_date', [$start, $end])
            ->whereNotNull('due_date')
            ->with(['case', 'assignedTo'])
            ->get();

        $timelineEvents = CaseTimelineEvent::whereBetween('event_date', [$start, $end])
            ->whereNotNull('event_date')
            ->with(['case', 'user'])
            ->get();

        $events = [];

        foreach ($tasks as $task) {
            $events[] = [
                'id' => 'task-'.$task->id,
                'title' => $task->title,
                'start' => $task->due_date->format('Y-m-d'),
                'end' => $task->due_date->format('Y-m-d'),
                'type' => 'task',
                'priority' => $task->priority,
                'status' => $task->status,
                'case' => $task->case?->case_number,
                'className' => 'event-task event-priority-'.$task->priority,
                'url' => route('tasks.show', $task),
            ];
        }

        foreach ($timelineEvents as $event) {
            $events[] = [
                'id' => 'event-'.$event->id,
                'title' => $event->title,
                'start' => $event->event_date->format('Y-m-d'),
                'end' => $event->event_date->format('Y-m-d'),
                'type' => $event->event_type,
                'milestone' => $event->is_milestone,
                'case' => $event->case?->case_number,
                'className' => $event->is_milestone ? 'event-milestone' : 'event-'.$event->event_type,
                'url' => $event->case ? route('cases.show', $event->case) : '#',
            ];
        }

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable',
            'type' => 'required|string',
            'case_id' => 'nullable|exists:cases,id',
        ]);

        $dateTime = $request->date;
        if ($request->time) {
            $dateTime .= ' '.$request->time;
        }

        if ($request->type === 'task') {
            CaseTask::create([
                'title' => $request->title,
                'due_date' => $dateTime,
                'case_id' => $request->case_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'priority' => 'medium',
            ]);
        } else {
            CaseTimelineEvent::create([
                'title' => $request->title,
                'event_date' => $dateTime,
                'event_type' => $request->type,
                'case_id' => $request->case_id,
                'user_id' => Auth::id(),
                'is_milestone' => $request->type === 'milestone',
            ]);
        }

        return redirect()->back()->with('success', 'Event created successfully');
    }
}
