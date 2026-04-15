<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">System overview and management</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users || 0 }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-green-600 dark:text-green-400">+{{ stats.newUsersThisMonth || 0 }} this month</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Cases</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.cases || 0 }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ stats.activeCases || 0 }} active</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Clients</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.clients || 0 }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ stats.newClientsThisMonth || 0 }} new this month</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ formatNumber(stats.revenue || 0) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-green-600 dark:text-green-400">+{{ stats.revenueThisMonth || 0 }} this month</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                <div class="space-y-4">
                    <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-4 pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div :class="activity.iconColor" class="p-2 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="activity.icon"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white">{{ activity.description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity.time }}</p>
                        </div>
                    </div>
                    <div v-if="recentActivity.length === 0" class="text-center py-4 text-gray-500">
                        No recent activity
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <router-link to="/clients" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <svg class="w-6 h-6 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <p class="font-medium text-gray-900 dark:text-white">Add Client</p>
                    </router-link>
                    <router-link to="/cases" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <svg class="w-6 h-6 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-medium text-gray-900 dark:text-white">New Case</p>
                    </router-link>
                    <router-link to="/leads" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <svg class="w-6 h-6 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <p class="font-medium text-gray-900 dark:text-white">View Leads</p>
                    </router-link>
                    <router-link to="/billing" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <svg class="w-6 h-6 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        <p class="font-medium text-gray-900 dark:text-white">Create Invoice</p>
                    </router-link>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Users by Role</h2>
                <div class="space-y-3">
                    <div v-for="role in usersByRole" :key="role.name" class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div :class="role.color" class="w-3 h-3 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ role.name }}</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">{{ role.count }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cases by Status</h2>
                <div class="space-y-3">
                    <div v-for="status in casesByStatus" :key="status.name" class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div :class="status.color" class="w-3 h-3 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ status.name }}</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">{{ status.count }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Health</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                        <span class="flex items-center text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                            Connected
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cache</span>
                        <span class="flex items-center text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                            Active
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Storage</span>
                        <span class="flex items-center text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                            Available
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '../../layouts/AppLayout.vue';

const stats = ref({});
const recentActivity = ref([]);
const usersByRole = ref([
    { name: 'Admins', count: 0, color: 'bg-red-500' },
    { name: 'Attorneys', count: 0, color: 'bg-blue-500' },
    { name: 'Paralegals', count: 0, color: 'bg-green-500' },
    { name: 'Clients', count: 0, color: 'bg-gray-500' },
]);

const casesByStatus = ref([]);

const formatNumber = (num) => {
    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

onMounted(async () => {
    try {
        const response = await axios.get('/api/admin/dashboard');
        stats.value = response.data.stats;
        recentActivity.value = response.data.recent_activity || [];
        
        const statusCounts = {};
        (response.data.cases_by_status || []).forEach(item => {
            statusCounts[item.status] = item.count;
        });
        
        casesByStatus.value = [
            { name: 'Active', count: statusCounts['Active'] || 0, color: 'bg-blue-500' },
            { name: 'Pending', count: statusCounts['Pending'] || 0, color: 'bg-yellow-500' },
            { name: 'Closed', count: statusCounts['Closed'] || 0, color: 'bg-green-500' },
            { name: 'On Hold', count: statusCounts['On Hold'] || 0, color: 'bg-orange-500' },
        ];
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
});
</script>
</script>
