<?php

namespace App\Http\Controllers;

use App\Models\CaseTask;
use App\Models\LegalCase;
use App\Models\User;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = CaseTask::with(['case', 'assignedTo', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->orderBy('due_date')->paginate(15);
        $users = User::whereIn('role', ['admin', 'attorney', 'paralegal'])->get();

        return view('tasks.index', compact('tasks', 'users'));
    }

    public function create(): View
    {
        $cases = LegalCase::with('assignedAttorney')->get();
        $users = User::whereIn('role', ['admin', 'attorney', 'paralegal'])->get();
        return view('tasks.create', compact('cases', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        CaseTask::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(CaseTask $task): View
    {
        $task->load(['case', 'assignedTo', 'createdBy', 'comments.user']);
        return view('tasks.show', compact('task'));
    }

    public function edit(CaseTask $task): View
    {
        $cases = LegalCase::with('assignedAttorney')->get();
        $users = User::whereIn('role', ['admin', 'attorney', 'paralegal'])->get();
        return view('tasks.edit', compact('task', 'cases', 'users'));
    }

    public function update(Request $request, CaseTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function updateStatus(Request $request, CaseTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task status updated.');
    }

    public function destroy(CaseTask $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function myTasks(): View
    {
        $tasks = CaseTask::with(['case'])
            ->where('assigned_to', auth()->id())
            ->orderBy('due_date')
            ->paginate(15);

        return view('tasks.my-tasks', compact('tasks'));
    }

    public function caseTasks(LegalCase $case): View
    {
        $tasks = $case->tasks()->with('assignedTo')->get();
        return view('tasks.case-tasks', compact('case', 'tasks'));
    }

    public function storeComment(Request $request, CaseTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Comment added.');
    }
}