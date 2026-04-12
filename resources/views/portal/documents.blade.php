<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Documents') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Case</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                <tr>
                                    <td>{{ $doc->name }}</td>
                                    <td>{{ $doc->case->case_number ?? 'N/A' }}</td>
                                    <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No documents found.</td></tr>
                        @endforelse
                    </table>
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>