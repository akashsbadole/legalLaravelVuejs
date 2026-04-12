<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Cases') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">Case Number</th>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Attorney</th>
                                <th class="px-4 py-3">Open Date</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cases as $case)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $case->case_number }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $case->title }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">{{ $case->status->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $case->assignedAttorney->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $case->open_date?->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('portal.cases.show', $case) }}" class="px-3 py-1.5 text-sm rounded-lg border border-primary-200 dark:border-primary-600 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900 transition">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No cases found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cases->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $cases->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>