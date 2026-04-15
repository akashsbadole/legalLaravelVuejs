<template>
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tasks</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your tasks</p>
            </div>
            <button @click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Task
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <select v-model="filters.priority" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <select v-model="filters.status" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
                <input v-model="filters.search" type="text" placeholder="Search tasks..." 
                       class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Tasks List -->
        <div class="space-y-4">
            <div v-for="task in filteredTasks" :key="task.id" 
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-start">
                    <button @click="toggleTask(task)" 
                            :class="task.completed || task.status === 'completed' ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300 dark:border-gray-600'"
                            class="w-6 h-6 rounded-full border-2 flex items-center justify-center mt-1 flex-shrink-0">
                        <svg v-if="task.completed || task.status === 'completed'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="ml-4 flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 :class="task.completed || task.status === 'completed' ? 'line-through text-gray-500' : 'text-gray-900 dark:text-white'" 
                                    class="text-lg font-medium">{{ task.title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ task.description || 'No description' }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span :class="getPriorityClass(task.priority)" class="px-2 py-1 text-xs rounded-full">{{ task.priority || 'medium' }}</span>
                                <button @click="deleteTask(task.id)" class="text-red-500 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <span v-if="task.case">Case: {{ task.case.title }}</span>
                            <span v-if="task.due_date">Due: {{ formatDate(task.due_date) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!loading && filteredTasks.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p>No tasks found</p>
                <button @click="openCreateModal" class="mt-4 text-blue-600 hover:text-blue-500">Create your first task</button>
            </div>

            <div v-if="loading" class="text-center py-12">
                <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
            </div>
        </div>

        <!-- Create Task Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6 z-10">
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Create Task</h2>
                    <form @submit.prevent="saveTask">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                                <input v-model="form.title" type="text" required 
                                       class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="form.description" rows="2" 
                                          class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                                    <input v-model="form.due_date" type="date" 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <select v-model="form.priority" 
                                            class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '../layouts/AppLayout.vue';

const tasks = ref([]);
const loading = ref(true);
const showModal = ref(false);

const filters = ref({ priority: '', status: '', search: '' });

const form = ref({ title: '', description: '', due_date: '', priority: 'medium' });

const filteredTasks = computed(() => {
    return tasks.value.filter(task => {
        const matchPriority = !filters.value.priority || task.priority === filters.value.priority;
        const matchStatus = !filters.value.status || 
            (filters.value.status === 'completed' && (task.completed || task.status === 'completed')) ||
            (filters.value.status === 'pending' && !task.completed && task.status !== 'completed');
        const matchSearch = !filters.value.search || task.title?.toLowerCase().includes(filters.value.search.toLowerCase());
        return matchPriority && matchStatus && matchSearch;
    });
});

onMounted(async () => {
    await fetchTasks();
});

async function fetchTasks() {
    loading.value = true;
    try {
        const response = await axios.get('/tasks');
        let data = response.data;
        if (data && typeof data === 'object' && !Array.isArray(data)) {
            data = data.data || [];
        }
        if (!Array.isArray(data)) {
            data = [];
        }
        tasks.value = data;
    } catch (error) {
        console.error('Error fetching tasks:', error);
        tasks.value = [];
    } finally {
        loading.value = false;
    }
}

function openCreateModal() {
    form.value = { title: '', description: '', due_date: '', priority: 'medium' };
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
}

async function saveTask() {
    try {
        await axios.post('/tasks', form.value);
        closeModal();
        await fetchTasks();
    } catch (error) {
        console.error('Error saving task:', error);
        alert('Failed to create task');
    }
}

async function toggleTask(task) {
    const newStatus = task.completed || task.status === 'completed' ? 'pending' : 'completed';
    try {
        await axios.patch(`/tasks/${task.id}`, { status: newStatus });
        task.status = newStatus;
        task.completed = newStatus === 'completed';
    } catch (error) {
        console.error('Error updating task:', error);
    }
}

async function deleteTask(id) {
    if (!confirm('Are you sure?')) return;
    try {
        await axios.delete(`/tasks/${id}`);
        await fetchTasks();
    } catch (error) {
        console.error('Error deleting task:', error);
    }
}

function getPriorityClass(priority) {
    const map = {
        'high': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'medium': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'low': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
    };
    return map[priority] || 'bg-gray-100 text-gray-700';
}

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
}
</script>
