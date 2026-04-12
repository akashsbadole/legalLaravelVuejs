<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tenants') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase">
                                <th class="pb-4">Name</th>
                                <th class="pb-4">Domain</th>
                                <th class="pb-4">Created</th>
                                <th class="pb-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($tenants as $tenant)
                                <tr>
                                    <td class="py-4">{{ $tenant->name }}</td>
                                    <td class="py-4">{{ $tenant->domain }}</td>
                                    <td class="py-4">{{ $tenant->created_at->format('M d, Y') }}</td>
                                    <td class="py-4">
                                        <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this tenant?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">No tenants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>