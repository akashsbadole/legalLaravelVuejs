<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Client Portal</h1>
                <p class="text-gray-600 dark:text-gray-400">Welcome to your client portal</p>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>

            <div v-else>
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.activeCases }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Invoices</p>
                    <p class="text-2xl font-bold text-yellow-600">${{ formatNumber(stats.pendingAmount) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Documents</p>
                    <p class="text-2xl font-bold text-blue-600">{{ stats.documents }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Cases -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Cases</h2>
                    <div class="space-y-3">
                        <div v-for="caseItem in recentCases" :key="caseItem.id"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ caseItem.title }}</h3>
                                <span :class="getStatusClass(caseItem.status)" class="px-2 py-0.5 text-xs rounded-full">{{ caseItem.status }}</span>
                            </div>
                            <p class="text-sm text-gray-500">{{ caseItem.description }}</p>
                            <p class="text-xs text-gray-400 mt-2">Last updated: {{ formatDate(caseItem.updated_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Invoices -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending Invoices</h2>
                    <div class="space-y-3">
                        <div v-for="invoice in pendingInvoices" :key="invoice.id"
                            class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ invoice.number }}</p>
                                <p class="text-sm text-gray-500">Due: {{ formatDate(invoice.due_date) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900 dark:text-white">${{ formatNumber(invoice.amount) }}</p>
                                <button class="text-sm text-primary-600 hover:text-primary-700">Pay Now</button>
                            </div>
                        </div>
                        <div v-if="pendingInvoices.length === 0" class="text-center py-4 text-gray-500">
                            No pending invoices
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Messages</h2>
                    <div class="space-y-3">
                        <div v-for="message in messages" :key="message.id"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 mr-3">
                                    {{ message.from.charAt(0) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ message.from }}</p>
                                        <span class="text-xs text-gray-400">{{ formatTime(message.sent_at) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ message.subject }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Documents</h2>
                    <div class="space-y-3">
                        <div v-for="doc in documents" :key="doc.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ doc.name }}</p>
                                    <p class="text-xs text-gray-500">{{ doc.date }}</p>
                                </div>
                            </div>
                            <button class="text-primary-600 hover:text-primary-700">Download</button>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import axios from 'axios';

const stats = ref({ activeCases: 0, pendingAmount: 0, documents: 0 });
const recentCases = ref([]);
const pendingInvoices = ref([]);
const messages = ref([]);
const documents = ref([]);
const loading = ref(true);

const loadDashboard = async () => {
    try {
        const response = await axios.get('/api/portal/dashboard');
        stats.value = response.data.stats;
        recentCases.value = response.data.recent_cases;
        pendingInvoices.value = response.data.pending_invoices;
        documents.value = response.data.documents;
    } catch (error) {
        console.error('Failed to load portal data:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadDashboard();
});

function formatNumber(num) {
    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function formatTime(date) {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const hours = Math.floor(diff / 3600000);
    if (hours < 24) return `${hours}h ago`;
    return d.toLocaleDateString();
}

function getStatusClass(status) {
    const statusLower = (status || '').toLowerCase();
    return {
        active: 'bg-green-100 text-green-800',
        open: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        closed: 'bg-gray-100 text-gray-800',
        completed: 'bg-gray-100 text-gray-800'
    }[statusLower] || 'bg-gray-100 text-gray-800';
}
</script>
