<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Enquiry Details') }} - {{ $enquiry->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 text-sm rounded-full {{ $enquiry->getStatusColor() }}">
                        {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full {{ $enquiry->getPriorityColor() }}">
                        {{ ucfirst($enquiry->priority) }}
                    </span>
                </div>
                <a href="{{ route('admin.enquiries.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Back to Enquiries</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Enquiry Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Enquiry Information</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $enquiry->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="text-gray-900 dark:text-white">{{ $enquiry->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                    <p class="text-gray-900 dark:text-white">{{ $enquiry->phone ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                                    <p class="text-gray-900 dark:text-white">{{ $enquiry->getCategoryDisplay() }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Priority</p>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $enquiry->getPriorityColor() }}">
                                        {{ ucfirst($enquiry->priority) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Submitted</p>
                                    <p class="text-gray-900 dark:text-white">{{ $enquiry->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Message</p>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $enquiry->message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment & Status -->
                    @if($enquiry->status === 'new' || $enquiry->status === 'assigned')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Manage Enquiry</h3>

                        @if($enquiry->status === 'new')
                        <!-- Assignment Form -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Assign to Lawyer</h4>
                            <form method="POST" action="{{ route('admin.enquiries.assign', $enquiry) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Lawyer</label>
                                    <select name="assigned_to" id="assigned_to" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                        <option value="">Choose a lawyer...</option>
                                        @foreach(\App\Models\User::whereIn('role', ['admin', 'attorney'])->get() as $lawyer)
                                        <option value="{{ $lawyer->id }}" {{ $enquiry->assigned_to == $lawyer->id ? 'selected' : '' }}>
                                            {{ $lawyer->name }} ({{ ucfirst($lawyer->role) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assignment Notes (Optional)</label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Add any notes for the assigned lawyer..."></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Assign Enquiry</button>
                            </form>
                        </div>
                        @endif

                        <!-- Status Update -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Update Status</h4>
                            <form method="POST" action="{{ route('admin.enquiries.updateStatus', $enquiry) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                        <option value="new" {{ $enquiry->status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="assigned" {{ $enquiry->status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="in_progress" {{ $enquiry->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ $enquiry->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ $enquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Update Notes</label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Add notes about this status update...">{{ $enquiry->admin_notes }}</textarea>
                                </div>
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Update Status</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Assignment Info -->
                    @if($enquiry->assigned_to)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Details</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $enquiry->assignedUser->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned By</p>
                                <p class="text-gray-900 dark:text-white">{{ $enquiry->assignedBy?->name ?? 'System' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned At</p>
                                <p class="text-gray-900 dark:text-white">{{ $enquiry->assigned_at?->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="mailto:{{ $enquiry->email }}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Send Email
                            </a>
                            @if($enquiry->phone)
                            <a href="tel:{{ $enquiry->phone }}" class="w-full block text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Call Client
                            </a>
                            @endif
                            <button onclick="window.print()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Print Enquiry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>