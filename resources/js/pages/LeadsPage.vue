<template>
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Leads</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your potential clients</p>
            </div>
            <button @click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Lead
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Leads</p>
                <p class="text-2xl font-bold text-blue-600">{{ stats.total }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">New</p>
                <p class="text-2xl font-bold text-green-600">{{ stats.new }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Contacted</p>
                <p class="text-2xl font-bold text-yellow-600">{{ stats.contacted }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Qualified</p>
                <p class="text-2xl font-bold text-purple-600">{{ stats.qualified }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <select v-model="filters.status" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="proposal">Proposal</option>
                    <option value="won">Won</option>
                    <option value="lost">Lost</option>
                </select>
                <input v-model="filters.search" type="text" placeholder="Search leads..." 
                       class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Leads Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-for="lead in filteredLeads" :key="lead.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ lead.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <div>{{ lead.email }}</div>
                                <div>{{ lead.phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ lead.company || 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span :class="getStatusClass(lead.status)" class="px-2 py-1 text-xs rounded-full">{{ lead.status }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ lead.source || 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <button @click="editLead(lead)" class="text-blue-600 hover:text-blue-500">Edit</button>
                                    <button @click="updateStatus(lead)" class="text-green-600 hover:text-green-500">Status</button>
                                    <button @click="deleteLead(lead.id)" class="text-red-600 hover:text-red-500">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!loading && filteredLeads.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No leads found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="loading" class="p-8 text-center">
                <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6 z-10">
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ editingLead ? 'Edit Lead' : 'Add Lead' }}</h2>
                    <form @submit.prevent="saveLead">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                    <input v-model="form.name" type="text" required 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input v-model="form.email" type="email" 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input v-model="form.phone" type="text" 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                                    <input v-model="form.company" type="text" 
                                           class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select v-model="form.status" 
                                            class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="new">New</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="qualified">Qualified</option>
                                        <option value="proposal">Proposal</option>
                                        <option value="won">Won</option>
                                        <option value="lost">Lost</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Source</label>
                                    <select v-model="form.source" 
                                            class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select</option>
                                        <option value="website">Website</option>
                                        <option value="referral">Referral</option>
                                        <option value="cold_call">Cold Call</option>
                                        <option value="advertisement">Advertisement</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
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

const leads = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editingLead = ref(null);

const stats = ref({ total: 0, new: 0, contacted: 0, qualified: 0 });

const filters = ref({ status: '', search: '' });

const form = ref({ name: '', email: '', phone: '', company: '', status: 'new', source: '' });

const filteredLeads = computed(() => {
    return leads.value.filter(lead => {
        const matchStatus = !filters.value.status || lead.status === filters.value.status;
        const matchSearch = !filters.value.search || 
            lead.name?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            lead.email?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            lead.company?.toLowerCase().includes(filters.value.search.toLowerCase());
        return matchStatus && matchSearch;
    });
});

onMounted(async () => {
    await fetchLeads();
});

async function fetchLeads() {
    loading.value = true;
    try {
        const response = await axios.get('/leads');
        leads.value = response.data.data || response.data || [];
        calculateStats();
    } catch (error) {
        console.error('Error fetching leads:', error);
    } finally {
        loading.value = false;
    }
}

function calculateStats() {
    stats.value.total = leads.value.length;
    stats.value.new = leads.value.filter(l => l.status === 'new').length;
    stats.value.contacted = leads.value.filter(l => l.status === 'contacted').length;
    stats.value.qualified = leads.value.filter(l => l.status === 'qualified').length;
}

function openCreateModal() {
    editingLead.value = null;
    form.value = { name: '', email: '', phone: '', company: '', status: 'new', source: '' };
    showModal.value = true;
}

function editLead(lead) {
    editingLead.value = lead;
    form.value = { name: lead.name, email: lead.email, phone: lead.phone, company: lead.company, status: lead.status, source: lead.source };
    showModal.value = true;
}

async function saveLead() {
    try {
        if (editingLead.value) {
            await axios.put(`/leads/${editingLead.value.id}`, form.value);
        } else {
            await axios.post('/leads', form.value);
        }
        closeModal();
        await fetchLeads();
    } catch (error) {
        console.error('Error saving lead:', error);
        alert('Failed to save lead');
    }
}

async function updateStatus(lead) {
    const statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];
    const currentIndex = statuses.indexOf(lead.status);
    const nextStatus = statuses[(currentIndex + 1) % statuses.length];
    try {
        await axios.put(`/leads/${lead.id}`, { status: nextStatus });
        await fetchLeads();
    } catch (error) {
        console.error('Error updating status:', error);
    }
}

async function deleteLead(id) {
    if (!confirm('Are you sure?')) return;
    try {
        await axios.delete(`/leads/${id}`);
        await fetchLeads();
    } catch (error) {
        console.error('Error deleting lead:', error);
    }
}

function closeModal() {
    showModal.value = false;
    editingLead.value = null;
}

function getStatusClass(status) {
    const map = {
        'new': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'contacted': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        'qualified': 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
        'proposal': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'won': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
        'lost': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'
    };
    return map[status] || 'bg-gray-100 text-gray-700';
}
</script>
