<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tenants</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manage multi-tenant organizations</p>
                </div>
                <button @click="showModal = true" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Add Tenant
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Tenants</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Suspended</p>
                    <p class="text-2xl font-bold text-red-600">{{ stats.suspended }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Trial</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ stats.trial }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <select v-model="filters.status" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="trial">Trial</option>
                    </select>
                    <input v-model="filters.search" type="text" placeholder="Search tenant..."
                        class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <!-- Tenants Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tenant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Domain</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Users</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            <tr v-for="tenant in filteredTenants" :key="tenant.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 mr-3">
                                            {{ tenant.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ tenant.name }}</p>
                                            <p class="text-sm text-gray-500">{{ tenant.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ tenant.slug }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span :class="getPlanClass(tenant.subscription_plan)" class="px-2 py-1 text-xs rounded-full">{{ tenant.subscription_plan }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ tenant.users_count || 0 }}</td>
                                <td class="px-6 py-4">
                                    <span :class="getStatusClass(tenant.subscription_status)" class="px-2 py-1 text-xs rounded-full">{{ tenant.subscription_status }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(tenant.subscription_expires_at) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <button @click="viewTenant(tenant)" class="text-blue-600 hover:text-blue-500 mr-3">View</button>
                                    <button v-if="tenant.status === 'active'" @click="suspendTenant(tenant)" class="text-red-600 hover:text-red-500">Suspend</button>
                                    <button v-else @click="activateTenant(tenant)" class="text-green-600 hover:text-green-500">Activate</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New Tenant</h3>
                    <form @submit.prevent="createTenant" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Organization Name</label>
                            <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain</label>
                            <input v-model="form.domain" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plan</label>
                            <select v-model="form.plan" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                <option value="basic">Basic</option>
                                <option value="pro">Pro</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';

const showModal = ref(false);
const tenants = ref([]);

const stats = ref({ total: 5, active: 3, suspended: 1, trial: 1 });

const filters = reactive({ status: '', search: '' });

const form = reactive({ name: '', email: '', domain: '', plan: 'basic' });

const filteredTenants = computed(() => {
    return tenants.value.filter(t => {
        const matchStatus = !filters.status || t.subscription_status === filters.status;
        const matchSearch = !filters.search || t.name.toLowerCase().includes(filters.search.toLowerCase()) || (t.slug || '').toLowerCase().includes(filters.search.toLowerCase());
        return matchStatus && matchSearch;
    });
});

const loadTenants = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/tenants');
        tenants.value = response.data.tenants || [];
    } catch (error) {
        console.error('Failed to load tenants:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadTenants();
});

function formatDate(date) { return new Date(date).toLocaleDateString(); }
function getPlanClass(plan) {
    return { enterprise: 'bg-purple-100 text-purple-800', professional: 'bg-blue-100 text-blue-800', basic: 'bg-gray-100 text-gray-800' }[plan] || '';
}
function getStatusClass(status) {
    return { active: 'bg-green-100 text-green-800', suspended: 'bg-red-100 text-red-800', trial: 'bg-yellow-100 text-yellow-800', cancelled: 'bg-gray-100 text-gray-800' }[status] || '';
}
function viewTenant(t) { alert(`Viewing: ${t.name}`); }
async function suspendTenant(t) {
    try {
        await axios.put(`/api/tenants/${t.id}`, { subscription_status: 'suspended' });
        t.subscription_status = 'suspended';
    } catch (error) {
        console.error('Failed to suspend tenant:', error);
    }
}
async function activateTenant(t) {
    try {
        await axios.put(`/api/tenants/${t.id}`, { subscription_status: 'active' });
        t.subscription_status = 'active';
    } catch (error) {
        console.error('Failed to activate tenant:', error);
    }
}
function createTenant() { alert('Tenant created!'); showModal.value = false; }
</script>
