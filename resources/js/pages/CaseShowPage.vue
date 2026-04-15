<template>
    <AppLayout>
        <div class="mb-6">
            <router-link to="/cases" class="text-blue-600 hover:text-blue-500 flex items-center mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back to Cases
            </router-link>
            <div v-if="loading" class="animate-pulse space-y-4">
                <div class="h-8 bg-gray-200 rounded w-1/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
            <div v-else-if="caseItem" class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ caseItem.title }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ caseItem.case_number }}</p>
                </div>
                <span :class="getStatusClass(caseItem.status)" class="px-3 py-1 text-sm rounded-full">{{ caseItem.status }}</span>
            </div>
        </div>

        <div v-if="caseItem" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <button v-for="tab in tabs" :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="activeTab === tab.id 
                                ? 'border-primary-500 text-primary-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-3 text-sm font-medium border-b-2">
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div v-if="activeTab === 'details'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Information</h3>
                                <dl class="space-y-3">
                                    <div class="flex">
                                        <dt class="w-32 text-gray-500 dark:text-gray-400">Case Number:</dt>
                                        <dd class="text-gray-900 dark:text-white font-medium">{{ caseItem.case_number }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-32 text-gray-500 dark:text-gray-400">Type:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ caseItem.case_type || 'N/A' }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-32 text-gray-500 dark:text-gray-400">Status:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ caseItem.status }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-32 text-gray-500 dark:text-gray-400">Opened:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ formatDate(caseItem.open_date) }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-32 text-gray-500 dark:text-gray-400">Closed:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ formatDate(caseItem.close_date) || 'Ongoing' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ caseItem.description || 'No description provided.' }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'tasks'">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Case Tasks</h3>
                            <button @click="showTaskModal = true" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                Add Task
                            </button>
                        </div>
                        <div v-if="caseTasks.length === 0" class="text-center py-8 text-gray-500">
                            No tasks for this case yet.
                        </div>
                        <div v-else class="space-y-3">
                            <div v-for="task in caseTasks" :key="task.id"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <button @click="toggleTask(task)" 
                                        :class="task.completed_at ? 'bg-green-500 border-green-500' : 'border-gray-300'"
                                        class="w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center">
                                        <svg v-if="task.completed_at" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <div>
                                        <p :class="task.completed_at ? 'line-through text-gray-500' : 'text-gray-900 dark:text-white'" class="font-medium">
                                            {{ task.title }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ formatDate(task.due_date) }}</p>
                                    </div>
                                </div>
                                <span :class="getPriorityClass(task.priority)" class="px-2 py-1 text-xs rounded-full">
                                    {{ task.priority || 'Normal' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'timeline'">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Timeline</h3>
                        <div v-if="timeline.length === 0" class="text-center py-8 text-gray-500">
                            No activity recorded yet.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="event in timeline" :key="event.id" class="flex">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                                    <div class="w-0.5 h-full bg-gray-200 dark:bg-gray-700"></div>
                                </div>
                                <div class="ml-4 pb-4">
                                    <p class="text-gray-900 dark:text-white font-medium">{{ event.description }}</p>
                                    <p class="text-sm text-gray-500">{{ formatDateTime(event.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'team'">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Team Members</h3>
                        <div v-if="caseItem.attorney" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                {{ caseItem.attorney.name?.charAt(0) }}
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 dark:text-white">{{ caseItem.attorney.name }}</p>
                                <p class="text-sm text-gray-500">Assigned Attorney</p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">No attorney assigned.</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showTaskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Task</h3>
                <form @submit.prevent="addTask" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input v-model="newTask.title" type="text" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                            <input v-model="newTask.due_date" type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <select v-model="newTask.priority"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showTaskModal = false"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import AppLayout from '../layouts/AppLayout.vue';

const route = useRoute();
const caseItem = ref(null);
const loading = ref(true);
const activeTab = ref('details');
const caseTasks = ref([]);
const timeline = ref([]);
const showTaskModal = ref(false);

const tabs = [
    { id: 'details', label: 'Details' },
    { id: 'tasks', label: 'Tasks' },
    { id: 'timeline', label: 'Timeline' },
    { id: 'team', label: 'Team' }
];

const newTask = ref({
    title: '',
    due_date: '',
    priority: 'normal'
});

onMounted(async () => {
    try {
        const response = await axios.get(`cases/${route.params.id}`);
        caseItem.value = response.data;
        
        const tasksRes = await axios.get(`tasks?case_id=${route.params.id}`);
        caseTasks.value = tasksRes.data;
        
        try {
            const timelineRes = await axios.get(`/api/cases/${route.params.id}/timeline`);
            timeline.value = timelineRes.data.timeline || [];
        } catch (e) {
            timeline.value = [
                { id: 1, description: `Case "${caseItem.value.title}" created`, created_at: caseItem.value.created_at },
                { id: 2, description: 'Case assigned to attorney', created_at: caseItem.value.updated_at }
            ];
        }
    } catch (error) {
        console.error('Error fetching case:', error);
    } finally {
        loading.value = false;
    }
});

async function addTask() {
    try {
        const response = await axios.post('tasks', {
            ...newTask.value,
            case_id: route.params.id
        });
        caseTasks.value.unshift(response.data);
        showTaskModal.value = false;
        newTask.value = { title: '', due_date: '', priority: 'normal' };
    } catch (error) {
        console.error('Error adding task:', error);
    }
}

async function toggleTask(task) {
    try {
        await axios.patch(`tasks/${task.id}`, {
            completed_at: task.completed_at ? null : new Date().toISOString()
        });
        task.completed_at = task.completed_at ? null : new Date().toISOString();
    } catch (error) {
        console.error('Error toggling task:', error);
    }
}

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString() : '';
}

function formatDateTime(date) {
    return date ? new Date(date).toLocaleString() : '';
}

function getStatusClass(status) {
    const map = {
        'active': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'closed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        'on_hold': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
}

function getPriorityClass(priority) {
    const map = {
        'low': 'bg-gray-100 text-gray-600',
        'normal': 'bg-blue-100 text-blue-600',
        'high': 'bg-orange-100 text-orange-600',
        'urgent': 'bg-red-100 text-red-600'
    };
    return map[priority] || 'bg-gray-100 text-gray-600';
}
</script>
