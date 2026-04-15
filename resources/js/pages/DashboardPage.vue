<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ authStore.user?.name }}</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Cases</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.cases }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active Clients</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.clients }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pending Invoices</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.pendingInvoices }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tasks Due</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.tasksDue }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Cases -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Cases</h2>
                    <router-link to="/cases" class="text-blue-600 hover:text-blue-500 text-sm">View all</router-link>
                </div>
                <div v-if="loading" class="space-y-3">
                    <div v-for="i in 3" :key="i" class="animate-pulse flex items-center space-x-4">
                        <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                        <div class="flex-1 space-y-2"><div class="h-4 bg-gray-200 rounded w-3/4"></div><div class="h-3 bg-gray-200 rounded w-1/2"></div></div>
                    </div>
                </div>
                <div v-else-if="recentCases.length" class="space-y-4">
                    <router-link v-for="caseItem in recentCases" :key="caseItem.id" :to="`/cases/${caseItem.id}`"
                                 class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ caseItem.title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ caseItem.case_number }}</p>
                        </div>
                        <span :class="getStatusClass(caseItem.status?.name)" class="px-2 py-1 text-xs rounded-full">{{ caseItem.status?.name }}</span>
                    </router-link>
                </div>
                <p v-else class="text-gray-500 dark:text-gray-400 text-sm">No cases found</p>
            </div>

            <!-- Upcoming Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Tasks</h2>
                    <router-link to="/tasks" class="text-blue-600 hover:text-blue-500 text-sm">View all</router-link>
                </div>
                <div v-if="loading" class="space-y-3">
                    <div v-for="i in 3" :key="i" class="animate-pulse flex items-center space-x-4">
                        <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                        <div class="flex-1 space-y-2"><div class="h-4 bg-gray-200 rounded w-3/4"></div><div class="h-3 bg-gray-200 rounded w-1/2"></div></div>
                    </div>
                </div>
                <div v-else-if="upcomingTasks.length" class="space-y-3">
                    <div v-for="task in upcomingTasks" :key="task.id" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <button @click="toggleTask(task)" :class="task.completed || task.status === 'completed' ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300'"
                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center">
                            <svg v-if="task.completed || task.status === 'completed'" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="ml-4 flex-1">
                            <p :class="task.completed ? 'line-through text-gray-500' : 'text-gray-900 dark:text-white'" class="text-sm font-medium">{{ task.title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Due {{ formatDate(task.due_date) }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500 dark:text-gray-400 text-sm">No upcoming tasks</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <router-link to="/cases" class="flex flex-col items-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">New Case</span>
                </router-link>
                <router-link to="/clients" class="flex flex-col items-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Add Client</span>
                </router-link>
                <router-link to="/billing" class="flex flex-col items-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">New Invoice</span>
                </router-link>
                <router-link to="/tasks" class="flex flex-col items-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">New Task</span>
                </router-link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import useSeo from '@/composables/useSeo';
import AppLayout from '@/layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const { setSeo } = useSeo();
const loading = ref(true);
const recentCases = ref([]);
const upcomingTasks = ref([]);
const stats = ref({ cases: 0, clients: 0, pendingInvoices: 0, tasksDue: 0 });

onMounted(async () => {
    // Set SEO meta tags
    setSeo({
        title: 'Dashboard',
        description: 'Legal practice management dashboard. Monitor cases, clients, billing, tasks, and team performance at a glance.',
        url: window.location.href
    });
    
    await fetchDashboardData();
    loading.value = false;
});

async function fetchDashboardData() {
    try {
        const [casesRes, tasksRes, statsRes] = await Promise.all([
            axios.get('/cases'),
            axios.get('/tasks?upcoming=true'),
            axios.get('/dashboard/stats')
        ]);
        
        recentCases.value = (casesRes.data.data || casesRes.data || []).slice(0, 5);
        upcomingTasks.value = (tasksRes.data.data || tasksRes.data || []).slice(0, 5);
        
        if (statsRes.data) {
            stats.value = {
                cases: statsRes.data.cases || 0,
                clients: statsRes.data.clients || 0,
                pendingInvoices: statsRes.data.pendingInvoices || 0,
                tasksDue: statsRes.data.tasksDue || 0
            };
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
}

function getStatusClass(status) {
    const map = {
        'Open': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'Pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'Closed': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
    };
    return map[status] || 'bg-gray-100 text-gray-700';
}

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '';
}

async function toggleTask(task) {
    const newStatus = task.completed || task.status === 'completed' ? 'pending' : 'completed';
    try {
        await axios.patch(`tasks/${task.id}`, { status: newStatus });
        task.status = newStatus;
        task.completed = newStatus === 'completed';
    } catch (error) {
        console.error('Error updating task:', error);
    }
}
</script>
