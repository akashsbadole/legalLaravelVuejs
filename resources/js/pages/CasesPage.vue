<template>
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cases</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your legal cases</p>
            </div>
            <button @click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Case
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <input v-model="filters.search" type="text" placeholder="Search cases..." 
                       class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <select v-model="filters.status" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                </select>
                <select v-model="filters.type" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Types</option>
                    <option v-for="type in caseTypes" :key="type" :value="type">{{ type }}</option>
                </select>
            </div>
        </div>

        <!-- Cases Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Case #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-for="caseItem in filteredCases" :key="caseItem.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ caseItem.case_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <router-link :to="`/cases/${caseItem.id}`" class="hover:text-blue-600">{{ caseItem.title }}</router-link>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="getStatusClass(caseItem.status?.name)" class="px-2 py-1 text-xs rounded-full">
                                    {{ caseItem.status?.name || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ caseItem.case_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ caseItem.client?.name || 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(caseItem.created_at) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <router-link :to="`/cases/${caseItem.id}`" class="text-blue-600 hover:text-blue-500">View</router-link>
                                    <button @click="editCase(caseItem)" class="text-gray-600 hover:text-gray-900 dark:text-gray-400">Edit</button>
                                    <button @click="deleteCase(caseItem.id)" class="text-red-600 hover:text-red-500">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!loading && filteredCases.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No cases found</td>
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
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                        {{ editingCase ? 'Edit Case' : 'Create New Case' }}
                    </h2>
                    <form @submit.prevent="saveCase">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                                <input v-model="form.title" type="text" required 
                                       class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Case Type *</label>
                                    <select v-model="form.case_type" required 
                                            class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Type</option>
                                        <option value="Civil">Civil</option>
                                        <option value="Criminal">Criminal</option>
                                        <option value="Family">Family</option>
                                        <option value="Corporate">Corporate</option>
                                        <option value="Intellectual Property">Intellectual Property</option>
                                        <option value="Real Estate">Real Estate</option>
                                        <option value="Immigration">Immigration</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select v-model="form.status_id" 
                                            class="mt-1 block w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="form.description" rows="3" 
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
import useSeo from '@/composables/useSeo';
import AppLayout from '@/layouts/AppLayout.vue';

const { setSeo } = useSeo();
const cases = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editingCase = ref(null);
const statuses = ref([]);
const caseTypes = ref([]);

const loadCaseTypes = async () => {
  try {
    const response = await axios.get('/api/case-types');
    caseTypes.value = response.data.types || [];
  } catch (error) {
    console.error('Failed to load case types:', error);
    caseTypes.value = ['Civil', 'Criminal', 'Family', 'Corporate', 'Intellectual Property', 'Real Estate', 'Immigration', 'Other'];
  }
};
loadCaseTypes();

const filters = ref({
    search: '',
    status: '',
    type: ''
});

const form = ref({
    title: '',
    case_type: '',
    status_id: '',
    description: ''
});

const filteredCases = computed(() => {
    return cases.value.filter(c => {
        const matchSearch = !filters.value.search || 
            c.title?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            c.case_number?.toLowerCase().includes(filters.value.search.toLowerCase());
        const matchStatus = !filters.value.status || c.status_id == filters.value.status;
        const matchType = !filters.value.type || c.case_type === filters.value.type;
        return matchSearch && matchStatus && matchType;
    });
});

onMounted(async () => {
    setSeo({
        title: 'Cases',
        description: 'Manage all legal cases, track status, assign attorneys, and organize case files.',
        url: window.location.href
    });
    await fetchData();
});

async function fetchData() {
    loading.value = true;
    try {
        const [casesRes, statusesRes] = await Promise.all([
            axios.get('/cases'),
            axios.get('/case-statuses')
        ]);
        cases.value = casesRes.data.data || [];
        statuses.value = statusesRes.data || [];
        if (statuses.value.length && !form.value.status_id) {
            form.value.status_id = statuses.value[0].id;
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

function openCreateModal() {
    editingCase.value = null;
    form.value = { title: '', case_type: '', status_id: statuses.value[0]?.id || '', description: '' };
    showModal.value = true;
}

function editCase(caseItem) {
    editingCase.value = caseItem;
    form.value = {
        title: caseItem.title,
        case_type: caseItem.case_type,
        status_id: caseItem.status_id,
        description: caseItem.description
    };
    showModal.value = true;
}

async function saveCase() {
    try {
        if (editingCase.value) {
            await axios.put(`/cases/${editingCase.value.id}`, form.value);
        } else {
            await axios.post('/cases', form.value);
        }
        closeModal();
        await fetchData();
    } catch (error) {
        console.error('Error saving case:', error);
        alert('Failed to save case');
    }
}

async function deleteCase(id) {
    if (!confirm('Are you sure you want to delete this case?')) return;
    try {
        await axios.delete(`/cases/${id}`);
        await fetchData();
    } catch (error) {
        console.error('Error deleting case:', error);
    }
}

function closeModal() {
    showModal.value = false;
    editingCase.value = null;
}

function getStatusClass(status) {
    const map = {
        'Open': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'Pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'Closed': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'In Progress': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
    };
    return map[status] || 'bg-gray-100 text-gray-700';
}

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
}
</script>
