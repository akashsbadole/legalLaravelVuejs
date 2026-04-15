<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Search</h1>
                <p class="text-gray-600 dark:text-gray-400">Search across cases, clients, documents, and more</p>
            </div>

            <!-- Search Input -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="query" @input="performSearch" type="text" placeholder="Type to search..."
                        class="w-full pl-12 pr-4 py-4 text-lg border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <button v-for="filter in filters" :key="filter.id"
                        @click="activeFilter = filter.id"
                        :class="activeFilter === filter.id ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        {{ filter.name }}
                    </button>
                </div>
            </div>

            <!-- Results -->
            <div v-if="query.length > 0">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ results.length }} results found</p>
                
                <div class="space-y-4">
                    <div v-for="result in results" :key="result.id"
                        @click="openResult(result)"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 cursor-pointer hover:shadow-md transition-shadow">
                        <div class="flex items-start">
                            <div :class="getTypeColor(result.type)" class="p-2 rounded-lg mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTypeIcon(result.type)"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ result.title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ result.description }}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span :class="getTypeBadge(result.type)" class="px-2 py-0.5 text-xs rounded-full">{{ result.type }}</span>
                                    <span class="text-xs text-gray-400">{{ formatDate(result.date) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="results.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No results found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try different keywords or filters</p>
                    </div>
                </div>
            </div>

            <!-- Recent Searches -->
            <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="font-medium text-gray-900 dark:text-white mb-4">Recent Searches</h3>
                <div class="space-y-2">
                    <div v-for="search in recentSearches" :key="search.id"
                        @click="query = search.term; performSearch()"
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">{{ search.term }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ search.count }} results</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';

const query = ref('');
const activeFilter = ref('all');
const results = ref([]);

const filters = [
    { id: 'all', name: 'All' },
    { id: 'cases', name: 'Cases' },
    { id: 'clients', name: 'Clients' },
    { id: 'documents', name: 'Documents' },
    { id: 'tasks', name: 'Tasks' },
];

const recentSearches = ref([
    { id: 1, term: 'Smith vs. Johnson', count: 5 },
    { id: 2, term: 'Contract template', count: 3 },
    { id: 3, term: 'John Smith', count: 8 },
]);

const allResults = [
    { id: 1, type: 'case', title: 'Smith vs. Johnson', description: 'Civil litigation case - Active', date: '2026-04-10' },
    { id: 2, type: 'case', title: 'Estate Planning - Brown', description: 'Wills and trusts case - Closed', date: '2026-04-05' },
    { id: 3, type: 'client', title: 'John Smith', description: 'Client since 2024 - 3 active cases', date: '2024-03-15' },
    { id: 4, type: 'client', title: 'Sarah Johnson', description: 'Client since 2025 - 1 active case', date: '2025-06-20' },
    { id: 5, type: 'document', title: 'Contract Agreement.pdf', description: 'Last modified 2 days ago', date: '2026-04-11' },
    { id: 6, type: 'task', title: 'Review contract', description: 'Due in 2 days - High priority', date: '2026-04-15' },
];

function performSearch() {
    if (query.value.length < 2) {
        results.value = [];
        return;
    }
    
    const q = query.value.toLowerCase();
    results.value = allResults.filter(r => {
        const matchesQuery = r.title.toLowerCase().includes(q) || r.description.toLowerCase().includes(q);
        const matchesFilter = activeFilter.value === 'all' || r.type === activeFilter.value;
        return matchesQuery && matchesFilter;
    });
}

function openResult(result) {
    alert(`Opening ${result.type}: ${result.title}`);
}

function getTypeColor(type) {
    const colors = {
        case: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        client: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        document: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
        task: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300'
    };
    return colors[type] || colors.case;
}

function getTypeIcon(type) {
    const icons = {
        case: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        client: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        document: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        task: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
    };
    return icons[type] || icons.case;
}

function getTypeBadge(type) {
    const badges = {
        case: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        client: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        document: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        task: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
    };
    return badges[type] || badges.case;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}
</script>
