<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Case: ') . $case->case_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $case->title }}</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('cases.timeline', $case) }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Timeline</a>
                    <a href="{{ route('cases.edit', $case) }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Edit</a>
                    <form method="POST" action="{{ route('cases.destroy', $case) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this case?')" class="px-4 py-2 rounded-xl bg-red-600 dark:bg-red-500 text-white hover:bg-red-700 dark:hover:bg-red-600 transition">Delete</button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $case->status->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->case_type ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Attorney</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->assignedAttorney->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Open Date</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->open_date?->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Expected Close</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->expected_close_date?->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Estimated Value</p>
                                <p class="text-gray-900 dark:text-white">{{ $case->estimated_value ? '$' . number_format($case->estimated_value, 2) : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                            <p class="text-gray-900 dark:text-white">{{ $case->description ?? 'No description' }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notes</h3>
                        </div>
                        <div class="p-6">
                            @forelse(($case->notes ?? collect())->sortByDesc('created_at') as $note)
                            <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $note->user->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $note->created_at?->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300">{{ $note->content }}</p>
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400">No notes yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Communications -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Communications</h3>
                            <div class="flex space-x-2">
                                <button onclick="document.getElementById('callModal').classList.remove('hidden')" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Log Call</button>
                                <button onclick="document.getElementById('followupModal').classList.remove('hidden')" class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">Schedule Follow-up</button>
                            </div>
                        </div>
                        <div class="p-6">
                            <!-- Follow-ups -->
                            <div class="mb-6">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Upcoming Follow-ups</h4>
                                @forelse($case->followUps->where('status', 'pending')->sortBy('scheduled_at') as $followup)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 mb-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white">{{ $followup->title }}</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $followup->description }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Due: {{ $followup->scheduled_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <form method="POST" action="{{ route('cases.updateFollowUp', $followup) }}" class="ml-2">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded">Mark Complete</button>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No upcoming follow-ups.</p>
                                @endforelse
                            </div>

                            <!-- Recent Interactions -->
                            <div>
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Recent Communications</h4>
                                @forelse($interactions as $interaction)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 mb-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $interaction->type === 'call' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($interaction->type) }}
                                            </span>
                                            @if($interaction->subject)
                                            <h5 class="font-medium text-gray-900 dark:text-white mt-1">{{ $interaction->subject }}</h5>
                                            @endif
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $interaction->sent_at->format('M d, Y H:i') }}</p>
                                            @if($interaction->call_duration)
                                            <p class="text-xs text-gray-600 dark:text-gray-300">Duration: {{ $interaction->callDurationFormatted }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No recent communications.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Client</span>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $case->client_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $case->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    @can('update', $case)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Staff Assignment</h3>
                        <form method="POST" action="{{ route('cases.assignStaff', $case) }}">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="assigned_attorney_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lead Attorney</label>
                                    <select name="assigned_attorney_id" id="assigned_attorney_id" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <option value="">Unassigned</option>
                                        @foreach(\App\Models\User::where('role', 'attorney')->orWhere('role', 'admin')->get() as $attorney)
                                        <option value="{{ $attorney->id }}" {{ $case->assigned_attorney_id == $attorney->id ? 'selected' : '' }}>{{ $attorney->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned Staff</label>
                                    <div class="mt-2 space-y-2">
                                        @foreach(\App\Models\User::whereIn('role', ['attorney', 'paralegal'])->get() as $staff)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="assigned_staff[]" value="{{ $staff->id }}"
                                                   {{ in_array($staff->id, $case->assigned_staff ?? []) ? 'checked' : '' }}
                                                   class="mr-2">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $staff->name }} ({{ ucfirst($staff->role) }})</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Assignment</button>
                            </div>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Call Logging Modal -->
    <div id="callModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Log Call</h3>
                <form method="POST" action="{{ route('cases.logCall', $case) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="direction" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Direction</label>
                            <select name="direction" id="direction" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                <option value="outbound">Outbound</option>
                                <option value="inbound">Inbound</option>
                            </select>
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                        </div>
                        <div>
                            <label for="call_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (seconds)</label>
                            <input type="number" name="call_duration" id="call_duration" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="call_outcome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Outcome</label>
                            <select name="call_outcome" id="call_outcome" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="">Select outcome</option>
                                <option value="answered">Answered</option>
                                <option value="voicemail">Voicemail</option>
                                <option value="no_answer">No Answer</option>
                                <option value="busy">Busy</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                            <input type="text" name="subject" id="subject" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="call_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="call_notes" id="call_notes" rows="3" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="document.getElementById('callModal').classList.add('hidden')" class="px-4 py-2 text-gray-700 dark:text-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Log Call</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Follow-up Modal -->
    <div id="followupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Schedule Follow-up</h3>
                <form method="POST" action="{{ route('cases.createFollowUp', $case) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select name="type" id="type" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                <option value="deadline">Deadline Reminder</option>
                                <option value="hearing">Court Hearing</option>
                                <option value="meeting">Client Meeting</option>
                                <option value="review">Case Review</option>
                                <option value="client_followup">Client Follow-up</option>
                            </select>
                        </div>
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                        </div>
                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                        </div>
                        <div>
                            <label for="reminder_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reminder (Optional)</label>
                            <input type="datetime-local" name="reminder_at" id="reminder_at" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <select name="priority" id="priority" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign To (Optional)</label>
                            <select name="assigned_to" id="assigned_to" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="">Unassigned</option>
                                @foreach(\App\Models\User::whereIn('role', ['admin', 'attorney', 'paralegal'])->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="document.getElementById('followupModal').classList.add('hidden')" class="px-4 py-2 text-gray-700 dark:text-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>