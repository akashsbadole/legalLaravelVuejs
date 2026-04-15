<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trust Accounts</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manage client trust funds</p>
                </div>
                <button @click="showModal = true"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    New Transaction
                </button>
            </div>

            <!-- Loading/Error States -->
            <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Loading trust accounts...</p>
            </div>

            <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <div class="text-red-600 dark:text-red-400 mr-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-red-800 dark:text-red-200 font-medium">Error loading data</p>
                        <p class="text-red-700 dark:text-red-300 text-sm">{{ error }}</p>
                        <button @click="loadData" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-500 underline">Try again</button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ currencySymbol }}{{ formatMoney(stats.totalBalance) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Withdrawals</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ currencySymbol }}{{ formatMoney(stats.pendingWithdrawals) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Accounts</p>
                    <p class="text-2xl font-bold text-blue-600">{{ stats.activeAccounts }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
                    <p class="text-2xl font-bold text-green-600">{{ currencySymbol }}{{ formatMoney(stats.thisMonth) }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <select v-model="filters.type" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Types</option>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="transfer">Transfer</option>
                    </select>
                    <input v-model="filters.search" type="text" placeholder="Search client..."
                        class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <!-- Accounts Table -->
            <div v-if="!loading && !error" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reference #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            <tr v-for="account in filteredAccounts" :key="account.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ account.client }}</td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ account.accountNumber }}</td>
                                <td class="px-6 py-4 text-sm capitalize text-gray-600 dark:text-gray-400">{{ account.type }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ currencySymbol }}{{ formatMoney(account.amount) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ currencySymbol }}{{ formatMoney(account.current_balance) }}</td>
                                <td class="px-6 py-4">
                                    <span :class="account.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : account.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                                        class="px-2 py-1 text-xs rounded-full capitalize">
                                        {{ account.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(account.transaction_date) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <button @click="viewAccount(account)" class="text-blue-600 hover:text-blue-500 mr-3">View</button>
                                </td>
                            </tr>
                            <tr v-if="filteredAccounts.length === 0 && !trustStore.isLoading">
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No trust account transactions found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transaction Modal -->
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Transaction</h3>
                    <form @submit.prevent="createTransaction" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client <span class="text-red-500">*</span></label>
                            <select v-model="form.client_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Select a client...</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Type <span class="text-red-500">*</span></label>
                            <select v-model="form.type" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="transfer">Transfer to Operating</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">{{ currencySymbol }}</span>
                                <input v-model.number="form.amount" type="number" step="0.01" min="0.01" required
                                    class="pl-8 mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Date <span class="text-red-500">*</span></label>
                            <input v-model="form.transaction_date" type="date" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea v-model="form.description" rows="3" placeholder="Optional description..."
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="showModal = false" :disabled="loading"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg disabled:opacity-50">Cancel</button>
                            <button type="submit" :disabled="loading || !form.client_id || !form.amount"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                                {{ loading ? 'Creating...' : 'Create Transaction' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import { useAuthStore } from '../stores/auth';
import { useTrustAccountsStore } from '../stores/trustAccounts';

const router = useRouter();
const authStore = useAuthStore();
const trustStore = useTrustAccountsStore();

const showModal = ref(false);
const loading = ref(false);
const error = ref(null);

const currencySymbol = computed(() => authStore.currencySymbol || '$');

const stats = computed(() => trustStore.getStats);
const clients = computed(() => trustStore.clients);
const accounts = computed(() => trustStore.accounts);

const form = reactive({
    client_id: '',
    type: 'deposit',
    amount: 0,
    description: '',
    transaction_date: new Date().toISOString().split('T')[0] // Today's date
});

const filters = reactive({
    type: '',
    search: ''
});

const filteredAccounts = computed(() => {
    return accounts.value.filter(acc => {
        const matchType = !filters.type || acc.type === filters.type;
        const matchSearch = !filters.search || acc.client.toLowerCase().includes(filters.search.toLowerCase());
        return matchType && matchSearch;
    });
});

onMounted(async () => {
    await loadData();
});

watch(() => filters, async () => {
    await loadAccounts();
}, { deep: true });

async function loadData() {
    loading.value = true;
    error.value = null;

    try {
        await Promise.all([
            trustStore.fetchClients(),
            loadAccounts(),
            trustStore.fetchStats()
        ]);
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load data';
        console.error('Error loading trust accounts data:', err);
    } finally {
        loading.value = false;
    }
}

async function loadAccounts() {
    try {
        const params = {};
        if (filters.type) params.transaction_type = filters.type;
        await trustStore.fetchAccounts(params);
    } catch (err) {
        console.error('Error loading accounts:', err);
    }
}

function formatMoney(num) {
    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function viewAccount(account) {
    // Navigate to account details or show modal
    alert(`Viewing account: ${account.accountNumber}\nClient: ${account.client}\nBalance: ${currencySymbol.value}${formatMoney(account.balance)}`);
}

function openTransactionModal(account = null) {
    if (account) {
        form.client_id = account.client_id;
    } else {
        form.client_id = '';
    }
    form.type = 'deposit';
    form.amount = 0;
    form.description = '';
    form.transaction_date = new Date().toISOString().split('T')[0];
    showModal.value = true;
}

async function createTransaction() {
    if (!form.client_id || !form.amount || form.amount <= 0) {
        alert('Please fill in all required fields with valid values');
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const transactionData = {
            client_id: form.client_id,
            transaction_type: form.type,
            amount: parseFloat(form.amount),
            description: form.description || null,
            transaction_date: form.transaction_date
        };

        await trustStore.createTransaction(transactionData);

        showModal.value = false;

        // Reset form
        form.client_id = '';
        form.type = 'deposit';
        form.amount = 0;
        form.description = '';

        alert('Transaction created successfully!');

    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to create transaction';
        alert(`Error: ${error.value}`);
        console.error('Error creating transaction:', err);
    } finally {
        loading.value = false;
    }
}

function resetFilters() {
    filters.type = '';
    filters.search = '';
}
</script>
