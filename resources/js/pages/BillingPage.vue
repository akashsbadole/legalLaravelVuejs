<template>
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Billing & Invoices</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage invoices and payments</p>
            </div>
            <button @click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Invoice
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                <p class="text-2xl font-bold text-green-600">{{ currencySymbol }}{{ formatMoney(stats.totalRevenue) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ currencySymbol }}{{ formatMoney(stats.pendingAmount) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Overdue</p>
                <p class="text-2xl font-bold text-red-600">{{ currencySymbol }}{{ formatMoney(stats.overdueAmount) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Invoices</p>
                <p class="text-2xl font-bold text-blue-600">{{ stats.totalInvoices }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <select v-model="filters.status" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input v-model="filters.search" type="text" placeholder="Search invoice..." 
                       class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ invoice.invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ invoice.client?.name || 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ currencySymbol }}{{ formatMoney(invoice.total_amount || invoice.total) }}</td>
                            <td class="px-6 py-4">
                                <span :class="getStatusClass(invoice.status)" class="px-2 py-1 text-xs rounded-full">{{ invoice.status }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(invoice.due_date) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <button @click="markAsPaid(invoice)" v-if="invoice.status === 'pending'" class="text-green-600 hover:text-green-500">Mark Paid</button>
                                    <button @click="viewInvoice(invoice)" class="text-blue-600 hover:text-blue-500">View</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!loading && filteredInvoices.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No invoices found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="loading" class="p-8 text-center">
                <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
            </div>
        </div>

        <!-- Create Invoice Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl p-6 z-10 max-h-[90vh] overflow-y-auto">
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Create Invoice</h2>
                    <form @submit.prevent="saveInvoice">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client *</label>
                                <select v-model="form.client_id" required 
                                        class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Client</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
                                    <input v-model="form.issue_date" type="date" 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date *</label>
                                    <input v-model="form.due_date" type="date" required 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="form.description" rows="2" 
                                          class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount *</label>
                                <input v-model="form.amount" type="number" step="0.01" required 
                                       class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Invoice</button>
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
import { useAuthStore } from '../stores/auth';
import AppLayout from '../layouts/AppLayout.vue';

const authStore = useAuthStore();
const invoices = ref([]);
const clients = ref([]);
const loading = ref(true);
const showModal = ref(false);

const stats = ref({ totalRevenue: 0, pendingAmount: 0, overdueAmount: 0, totalInvoices: 0 });

const filters = ref({ status: '', search: '' });

const form = ref({
    client_id: '',
    amount: '',
    due_date: '',
    issue_date: new Date().toISOString().split('T')[0],
    description: ''
});

const filteredInvoices = computed(() => {
    return invoices.value.filter(inv => {
        const matchStatus = !filters.value.status || inv.status === filters.value.status;
        const matchSearch = !filters.value.search || 
            inv.invoice_number?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            inv.client?.name?.toLowerCase().includes(filters.value.search.toLowerCase());
        return matchStatus && matchSearch;
    });
});

const currencySymbol = computed(() => authStore.currencySymbol || '$');

onMounted(async () => {
    await fetchData();
});

async function fetchData() {
    loading.value = true;
    try {
        const [invoicesRes, clientsRes] = await Promise.all([
            axios.get('/invoices'),
            axios.get('/clients/all')
        ]);
        invoices.value = Array.isArray(invoicesRes.data) ? invoicesRes.data : (invoicesRes.data.data || []);
        clients.value = Array.isArray(clientsRes.data) ? clientsRes.data : (clientsRes.data.data || []);
        calculateStats();
    } catch (error) {
        console.error('Error fetching data:', error);
        alert('Failed to load billing data. Please check your connection.');
    } finally {
        loading.value = false;
    }
}

function calculateStats() {
    stats.value.totalInvoices = invoices.value.length;
    stats.value.totalRevenue = invoices.value.filter(i => i.status === 'paid').reduce((sum, i) => sum + (parseFloat(i.total_amount || i.total) || 0), 0);
    stats.value.pendingAmount = invoices.value.filter(i => i.status === 'pending').reduce((sum, i) => sum + (parseFloat(i.total_amount || i.total) || 0), 0);
    stats.value.overdueAmount = invoices.value.filter(i => i.status === 'overdue').reduce((sum, i) => sum + (parseFloat(i.total_amount || i.total) || 0), 0);
}

function openCreateModal() {
    form.value = { client_id: '', amount: '', due_date: '', issue_date: new Date().toISOString().split('T')[0], description: '' };
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
}

async function saveInvoice() {
    try {
        await axios.post('/invoices', {
            client_id: form.value.client_id,
            total: form.value.amount,
            due_date: form.value.due_date,
            issue_date: form.value.issue_date,
            description: form.value.description,
            status: 'pending'
        });
        closeModal();
        await fetchData();
    } catch (error) {
        console.error('Error saving invoice:', error);
        alert('Failed to create invoice');
    }
}

async function markAsPaid(invoice) {
    try {
        await axios.post(`/invoices/${invoice.id}/payment`);
        await fetchData();
    } catch (error) {
        console.error('Error marking as paid:', error);
    }
}

function viewInvoice(invoice) {
    alert(`Invoice: ${invoice.invoice_number}\nClient: ${invoice.client?.name}\nAmount: ${currencySymbol.value}${invoice.total_amount || invoice.total}\nStatus: ${invoice.status}`);
}

function getStatusClass(status) {
    const map = {
        'pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'paid': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'overdue': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'cancelled': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
    };
    return map[status] || 'bg-gray-100 text-gray-700';
}

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
}

function formatMoney(num) {
    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
