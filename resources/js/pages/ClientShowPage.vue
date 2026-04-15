<template>
    <AppLayout>
        <div class="mb-6">
            <router-link to="/clients" class="text-blue-600 hover:text-blue-500 flex items-center mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back to Clients
            </router-link>
            <div v-if="client" class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ client.name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ client.email }}</p>
                </div>
                <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ client.role }}
                </span>
            </div>
        </div>

        <div v-if="client" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px">
                    <button v-for="tab in tabs" :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="activeTab === tab.id 
                            ? 'border-primary-500 text-primary-600' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-3 text-sm font-medium border-b-2">
                        {{ tab.label }}
                        <span v-if="tab.count !== undefined" class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700">
                            {{ tab.count }}
                        </span>
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div v-if="activeTab === 'details'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                            <dl class="space-y-3">
                                <div class="flex">
                                    <dt class="w-24 text-gray-500 dark:text-gray-400">Name:</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ client.name }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-24 text-gray-500 dark:text-gray-400">Email:</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ client.email }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-24 text-gray-500 dark:text-gray-400">Phone:</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ client.phone || 'N/A' }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-24 text-gray-500 dark:text-gray-400">Address:</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ client.address || 'N/A' }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-24 text-gray-500 dark:text-gray-400">Role:</dt>
                                    <dd class="text-gray-900 dark:text-white capitalize">{{ client.role }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-24 text-gray-500 dark:text-gray-400">Member Since:</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ formatDate(client.created_at) }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-primary-600">{{ clientCases.length }}</p>
                                    <p class="text-sm text-gray-500">Active Cases</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-green-600">${{ totalPaid.toLocaleString() }}</p>
                                    <p class="text-sm text-gray-500">Total Paid</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-yellow-600">${{ totalPending.toLocaleString() }}</p>
                                    <p class="text-sm text-gray-500">Pending</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-blue-600">{{ clientInvoices.length }}</p>
                                    <p class="text-sm text-gray-500">Invoices</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'cases'">
                    <div v-if="clientCases.length === 0" class="text-center py-8 text-gray-500">
                        No cases for this client yet.
                    </div>
                    <div v-else class="space-y-4">
                        <router-link v-for="c in clientCases" :key="c.id" :to="`/cases/${c.id}`"
                            class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ c.title }}</h4>
                                    <p class="text-sm text-gray-500">{{ c.case_number }}</p>
                                </div>
                                <span :class="getStatusClass(c.status)" class="px-2 py-1 text-xs rounded-full">
                                    {{ c.status }}
                                </span>
                            </div>
                        </router-link>
                    </div>
                </div>

                <div v-if="activeTab === 'invoices'">
                    <div v-if="clientInvoices.length === 0" class="text-center py-8 text-gray-500">
                        No invoices for this client yet.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="inv in clientInvoices" :key="inv.id"
                            class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ inv.invoice_number }}</p>
                                <p class="text-sm text-gray-500">Due: {{ formatDate(inv.due_date) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(inv.total).toLocaleString() }}</p>
                                <span :class="getInvoiceStatusClass(inv.status)" class="px-2 py-1 text-xs rounded-full">
                                    {{ inv.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'activity'">
                    <div v-if="activities.length === 0" class="text-center py-8 text-gray-500">
                        No recent activity.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="act in activities" :key="act.id" class="flex items-start space-x-4">
                            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 dark:text-white">{{ act.description }}</p>
                                <p class="text-sm text-gray-500">{{ formatDateTime(act.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import AppLayout from '../layouts/AppLayout.vue';

const route = useRoute();
const client = ref(null);
const clientCases = ref([]);
const clientInvoices = ref([]);
const activities = ref([]);
const activeTab = ref('details');

const tabs = computed(() => [
    { id: 'details', label: 'Details' },
    { id: 'cases', label: 'Cases', count: clientCases.value.length },
    { id: 'invoices', label: 'Invoices', count: clientInvoices.value.length },
    { id: 'activity', label: 'Activity' }
]);

const totalPaid = computed(() => {
    return clientInvoices.value
        .filter(i => i.status === 'paid')
        .reduce((sum, i) => sum + parseFloat(i.total || 0), 0);
});

const totalPending = computed(() => {
    return clientInvoices.value
        .filter(i => i.status === 'pending')
        .reduce((sum, i) => sum + parseFloat(i.total || 0), 0);
});

onMounted(async () => {
    try {
        const clientRes = await axios.get(`clients/${route.params.id}`);
        client.value = clientRes.data;
        
        const casesRes = await axios.get('cases');
        clientCases.value = casesRes.data.filter(c => c.client_id === client.value.id);
        
        const invoicesRes = await axios.get('invoices');
        clientInvoices.value = invoicesRes.data.data?.filter(i => i.client_id === client.value.id) || [];
        
        try {
            const activitiesRes = await axios.get(`/api/clients/${client.value.id}/activities`);
            activities.value = activitiesRes.data.activities || [];
        } catch (e) {
            activities.value = [
                { id: 1, description: 'Client profile created', created_at: client.value.created_at },
                { id: 2, description: 'Welcome email sent', created_at: client.value.created_at }
            ];
        }
    } catch (error) {
        console.error('Error fetching client:', error);
    }
});

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
        'closed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
}

function getInvoiceStatusClass(status) {
    const map = {
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'paid': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'overdue': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
}
</script>
