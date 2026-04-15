<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
                    <p class="text-gray-600 dark:text-gray-400">Track all system activities and changes</p>
                </div>
                <button @click="exportLogs" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Export Logs
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <select v-model="filters.type" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Types</option>
                        <option value="user">User Actions</option>
                        <option value="case">Case Actions</option>
                        <option value="billing">Billing</option>
                        <option value="system">System</option>
                    </select>
                    <select v-model="filters.user" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Users</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    <input v-model="filters.date" type="date" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Timestamp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDateTime(log.created_at) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-sm mr-2">
                                            {{ log.user?.charAt(0) || 'S' }}
                                        </div>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ log.user }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getActionClass(log.action)" class="px-2 py-1 text-xs rounded-full">{{ log.action }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ log.module }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ log.description }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ log.ip }}</td>
                            </tr>
                            <tr v-if="filteredLogs.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No logs found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Showing {{ filteredLogs.length }} of {{ logs.length }} entries</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';

const logs = ref([]);
const users = ref([]);

const filters = reactive({ type: '', user: '', date: '' });

const filteredLogs = computed(() => {
    return logs.value.filter(log => {
        const matchType = !filters.type || log.module.toLowerCase() === filters.type.toLowerCase();
        const matchUser = !filters.user || log.user_id === filters.user;
        const matchDate = !filters.date || log.created_at.startsWith(filters.date);
        return matchType && matchUser && matchDate;
    });
});

onMounted(() => {
    users.value = [
        { id: 1, name: 'Admin User' },
        { id: 2, name: 'John Attorney' },
        { id: 3, name: 'Sarah Paralegal' },
    ];

    logs.value = [
        { id: 1, user_id: 1, user: 'Admin User', action: 'Create', module: 'Case', description: 'Created new case: Smith vs. Johnson', created_at: '2026-04-13T14:30:00', ip: '192.168.1.100' },
        { id: 2, user_id: 2, user: 'John Attorney', action: 'Update', module: 'Case', description: 'Updated case status to Active', created_at: '2026-04-13T14:25:00', ip: '192.168.1.101' },
        { id: 3, user_id: 1, user: 'Admin User', action: 'Login', module: 'System', description: 'User logged in successfully', created_at: '2026-04-13T14:20:00', ip: '192.168.1.100' },
        { id: 4, user_id: 3, user: 'Sarah Paralegal', action: 'Upload', module: 'Document', description: 'Uploaded: Contract_2024.pdf', created_at: '2026-04-13T13:45:00', ip: '192.168.1.102' },
        { id: 5, user_id: 2, user: 'John Attorney', action: 'Invoice', module: 'Billing', description: 'Created invoice INV-2024-0045', created_at: '2026-04-13T12:30:00', ip: '192.168.1.101' },
        { id: 6, user_id: 1, user: 'Admin User', action: 'Create', module: 'User', description: 'Created new user: test@example.com', created_at: '2026-04-13T11:15:00', ip: '192.168.1.100' },
        { id: 7, user_id: 3, user: 'Sarah Paralegal', action: 'Update', module: 'Task', description: 'Marked task as completed', created_at: '2026-04-13T10:00:00', ip: '192.168.1.102' },
    ];
});

function formatDateTime(date) {
    return new Date(date).toLocaleString();
}

function getActionClass(action) {
    const classes = {
        Create: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        Update: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        Delete: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        Login: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        Upload: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        Invoice: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    };
    return classes[action] || 'bg-gray-100 text-gray-800';
}

function exportLogs() {
    alert('Exporting audit logs to CSV...');
}
</script>
