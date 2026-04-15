<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CalendarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $events = CalendarEvent::with(['case', 'client'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->whereNotNull('end_date')
                    ->where('end_date', '>=', $startDate)
                    ->where('start_date', '<=', $endDate);
            })
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'type' => $event->type,
                    'start_date' => $event->start_date->toDateString(),
                    'start_time' => $event->start_time,
                    'end_date' => $event->end_date?->toDateString(),
                    'end_time' => $event->end_time,
                    'all_day' => $event->all_day,
                    'location' => $event->location,
                    'case_id' => $event->case_id,
                    'case' => $event->case ? [
                        'id' => $event->case->id,
                        'case_number' => $event->case->case_number,
                        'title' => $event->case->title
                    ] : null,
                    'client_id' => $event->client_id,
                    'client' => $event->client ? [
                        'id' => $event->client->id,
                        'name' => $event->client->name
                    ] : null,
                    'status' => $event->status,
                    'reminder' => $event->reminder,
                    'created_at' => $event->created_at
                ];
            });

        return response()->json(['events' => $events]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:meeting,case_hearing,task,deadline,reminder,other',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'case_id' => 'nullable|exists:legal_cases,id',
            'client_id' => 'nullable|exists:users,id',
            'reminder' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:5'
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'scheduled';

        $event = CalendarEvent::create($validated);

        return response()->json([
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'type' => $event->type,
                'start_date' => $event->start_date->toDateString(),
                'start_time' => $event->start_time,
                'status' => $event->status
            ],
            'message' => 'Event created successfully'
        ], 201);
    }

    public function show(CalendarEvent $calendar): JsonResponse
    {
        return response()->json([
            'event' => [
                'id' => $calendar->id,
                'title' => $calendar->title,
                'description' => $calendar->description,
                'type' => $calendar->type,
                'start_date' => $calendar->start_date->toDateString(),
                'start_time' => $calendar->start_time,
                'end_date' => $calendar->end_date?->toDateString(),
                'end_time' => $calendar->end_time,
                'all_day' => $calendar->all_day,
                'location' => $calendar->location,
                'case_id' => $calendar->case_id,
                'client_id' => $calendar->client_id,
                'status' => $calendar->status,
                'reminder' => $calendar->reminder,
                'reminder_minutes' => $calendar->reminder_minutes,
                'created_at' => $calendar->created_at
            ]
        ]);
    }

    public function update(Request $request, CalendarEvent $calendar): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:meeting,case_hearing,task,deadline,reminder,other',
            'start_date' => 'sometimes|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'case_id' => 'nullable|exists:legal_cases,id',
            'client_id' => 'nullable|exists:users,id',
            'status' => 'sometimes|in:scheduled,completed,cancelled',
            'reminder' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:5'
        ]);

        $calendar->update($validated);

        return response()->json([
            'event' => [
                'id' => $calendar->id,
                'title' => $calendar->title,
                'type' => $calendar->type,
                'status' => $calendar->status,
                'updated_at' => $calendar->updated_at
            ],
            'message' => 'Event updated successfully'
        ]);
    }

    public function destroy(CalendarEvent $calendar): JsonResponse
    {
        $calendar->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}