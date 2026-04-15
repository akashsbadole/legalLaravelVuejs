<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                    <p class="text-gray-600 dark:text-gray-400">Stay updated with important alerts</p>
                </div>
                <button v-if="hasUnread" @click="markAllRead" class="px-4 py-2 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-lg">
                    Mark all as read
                </button>
            </div>

            <!-- Filter -->
            <div class="flex gap-2 mb-6">
                <button v-for="filter in filters" :key="filter.id"
                    @click="activeFilter = filter.id"
                    :class="activeFilter === filter.id ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    {{ filter.name }}
                </button>
            </div>

            <!-- Notifications -->
            <div class="space-y-3">
                <div v-for="notification in filteredNotifications" :key="notification.id"
                    :class="notification.is_read ? 'bg-white dark:bg-gray-800' : 'bg-primary-50 dark:bg-primary-900/20'"
                    class="rounded-xl shadow-sm p-4 transition-colors">
                    <div class="flex items-start">
                        <div :class="getIconColor(notification.type)" class="p-2 rounded-lg mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIcon(notification.type)"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ notification.title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ notification.message }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-400">{{ formatTime(notification.created_at) }}</span>
                                    <button v-if="!notification.is_read" @click="markRead(notification)" class="text-primary-600 hover:text-primary-700 text-sm">Mark read</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="filteredNotifications.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';
import axios from 'axios';

const notifications = ref([]);
const activeFilter = ref('all');
const loading = ref(false);

const filters = [
    { id: 'all', name: 'All' },
    { id: 'unread', name: 'Unread' },
    { id: 'case_update', name: 'Cases' },
    { id: 'task_assigned', name: 'Tasks' },
    { id: 'invoice_received', name: 'Billing' },
];

const hasUnread = computed(() => notifications.value.some(n => !n.is_read));

const filteredNotifications = computed(() => {
    if (activeFilter.value === 'all') return notifications.value;
    if (activeFilter.value === 'unread') return notifications.value.filter(n => !n.is_read);
    return notifications.value.filter(n => n.type === activeFilter.value);
});

const loadNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/notifications');
        notifications.value = response.data.notifications || [];
    } catch (error) {
        console.error('Failed to load notifications:', error);
    } finally {
        loading.value = false;
    }
};

const markRead = async (notification) => {
    try {
        await axios.put(`/api/notifications/${notification.id}/read`);
        notification.is_read = true;
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllRead = async () => {
    try {
        await axios.put('/api/notifications/read-all');
        notifications.value.forEach(n => n.is_read = true);
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

function formatTime(date) {
    const diff = Date.now() - new Date(date).getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    return new Date(date).toLocaleDateString();
}

function getIcon(type) {
    const icons = {
        case_update: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        task_assigned: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        invoice_received: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    };
    return icons[type] || icons.case_update;
}

function getIconColor(type) {
    const colors = {
        case_update: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        task_assigned: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        invoice_received: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
    };
    return colors[type] || colors.case_update;
}

onMounted(() => {
    loadNotifications();
});
</script>
