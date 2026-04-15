<template>
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clients</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your clients</p>
            </div>
            <button @click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Client
            </button>
        </div>

        <!-- Search -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <input v-model="search" type="text" placeholder="Search by name or email..." 
                   class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <!-- Clients Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="client in filteredClients" :key="client.id" 
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <router-link :to="`/clients/${client.id}`">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg font-semibold">
                                {{ client.name?.charAt(0) || '?' }}
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ client.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ client.email }}</p>
                            </div>
                        </div>
                        <button @click.prevent="editClient(client)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 12a1 1 0 110-2 1 1 0 010 2zm6 3l2 2m-2-4l2 2m-10-6l2 2m2-4l-2 2" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ client.phone || 'No phone' }}
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ client.address || 'No address' }}
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Added {{ formatDate(client.created_at) }}</span>
                        <button @click.prevent="deleteClient(client.id)" class="text-red-500 hover:text-red-600 text-sm">Delete</button>
                    </div>
                </router-link>
            </div>
        </div>

        <div v-if="!loading && filteredClients.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p>No clients found</p>
            <button @click="openCreateModal" class="mt-4 text-blue-600 hover:text-blue-500">Add your first client</button>
        </div>

        <div v-if="loading" class="text-center py-12">
            <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6 z-10">
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                        {{ editingClient ? 'Edit Client' : 'Add New Client' }}
                    </h2>
                    <form @submit.prevent="saveClient">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                <input v-model="form.name" type="text" required 
                                       class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                                <input v-model="form.email" type="email" required 
                                       class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input v-model="form.phone" type="text" 
                                       class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <textarea v-model="form.address" rows="2" 
                                          class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
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

const clients = ref([]);
const loading = ref(true);
const search = ref('');
const showModal = ref(false);
const editingClient = ref(null);

const form = ref({
    name: '',
    email: '',
    phone: '',
    address: ''
});

const filteredClients = computed(() => {
    if (!search.value) return clients.value;
    const q = search.value.toLowerCase();
    return clients.value.filter(c => 
        c.name?.toLowerCase().includes(q) || c.email?.toLowerCase().includes(q)
    );
});

onMounted(async () => {
    await fetchClients();
});

async function fetchClients() {
    loading.value = true;
    try {
        const response = await axios.get('/clients/all');
        clients.value = response.data.data || response.data || [];
    } catch (error) {
        console.error('Error fetching clients:', error);
    } finally {
        loading.value = false;
    }
}

function openCreateModal() {
    editingClient.value = null;
    form.value = { name: '', email: '', phone: '', address: '' };
    showModal.value = true;
}

function editClient(client) {
    editingClient.value = client;
    form.value = { name: client.name, email: client.email, phone: client.phone || '', address: client.address || '' };
    showModal.value = true;
}

async function saveClient() {
    try {
        if (editingClient.value) {
            await axios.put(`/clients/${editingClient.value.id}`, form.value);
        } else {
            await axios.post('/clients', form.value);
        }
        closeModal();
        await fetchClients();
    } catch (error) {
        console.error('Error saving client:', error);
        alert('Failed to save client');
    }
}

async function deleteClient(id) {
    if (!confirm('Are you sure you want to delete this client?')) return;
    try {
        await axios.delete(`/clients/${id}`);
        await fetchClients();
    } catch (error) {
        console.error('Error deleting client:', error);
    }
}

function closeModal() {
    showModal.value = false;
    editingClient.value = null;
}

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
}
</script>
