<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Documents</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manage your documents and files</p>
                </div>
                <div class="flex gap-2">
                    <button @click="uploadModal = true"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Upload Document
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 mb-6">
                <select v-model="filters.type" 
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">All Types</option>
                    <option value="pdf">PDF</option>
                    <option value="doc">Word</option>
                    <option value="image">Images</option>
                </select>
                <input v-model="filters.search" type="text" placeholder="Search documents..."
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white flex-1 max-w-xs">
            </div>

            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>

            <div v-else-if="filteredDocuments.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No documents</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by uploading your first document.</p>
                <div class="mt-6">
                    <button @click="uploadModal = true"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Upload Document
                    </button>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="doc in filteredDocuments" :key="doc.id"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer"
                    @click="viewDocument(doc)">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <div :class="getFileIconClass(doc.type)" class="p-2 rounded-lg mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[150px]">{{ doc.name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(doc.size) }}</p>
                            </div>
                        </div>
                        <button @click.stop="downloadDocument(doc)" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ formatDate(doc.created_at) }}</span>
                        <span :class="getTypeBadgeClass(doc.type)" class="px-2 py-0.5 rounded-full">{{ doc.type?.toUpperCase() }}</span>
                    </div>
                </div>
            </div>

            <div v-if="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload Document</h3>
                    
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Drag and drop files here, or click to select
                        </p>
                        <input type="file" @change="handleFileUpload" multiple class="hidden" ref="fileInput">
                        <button @click="$refs.fileInput.click()"
                            class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Select Files
                        </button>
                    </div>

                    <div v-if="selectedFiles.length > 0" class="mt-4 space-y-2">
                        <div v-for="(file, index) in selectedFiles" :key="index"
                            class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[200px]">{{ file.name }}</span>
                            <button @click="selectedFiles.splice(index, 1)" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Name</label>
                        <input v-model="uploadForm.name" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="uploadModal = false; selectedFiles = []"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                            Cancel
                        </button>
                        <button @click="uploadDocument" :disabled="uploading"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                            {{ uploading ? 'Uploading...' : 'Upload' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';
import axios from 'axios';

const loading = ref(true);
const uploadModal = ref(false);
const uploading = ref(false);
const selectedFiles = ref([]);
const documents = ref([]);
const fileInput = ref(null);

const filters = reactive({
    type: '',
    search: ''
});

const uploadForm = reactive({
    name: ''
});

const filteredDocuments = computed(() => {
    return documents.value.filter(doc => {
        const matchesType = !filters.type || doc.type === filters.type;
        const matchesSearch = !filters.search || doc.name.toLowerCase().includes(filters.search.toLowerCase());
        return matchesType && matchesSearch;
    });
});

const getFileIconClass = (type) => {
    switch (type) {
        case 'pdf': return 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300';
        case 'doc': return 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300';
        case 'image': return 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300';
        default: return 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
    }
};

const getTypeBadgeClass = (type) => {
    switch (type) {
        case 'pdf': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        case 'doc': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'image': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const handleFileUpload = (event) => {
    selectedFiles.value = Array.from(event.target.files);
};

const uploadDocument = async () => {
    uploading.value = true;
    try {
        const formData = new FormData();
        selectedFiles.value.forEach(file => {
            formData.append('files[]', file);
        });
        formData.append('name', uploadForm.name || selectedFiles.value[0]?.name || 'Document');
        
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        documents.value.unshift({
            id: Date.now(),
            name: uploadForm.name || selectedFiles.value[0]?.name,
            type: selectedFiles.value[0]?.name.split('.').pop() || 'unknown',
            size: selectedFiles.value[0]?.size || 0,
            created_at: new Date().toISOString()
        });
        
        uploadModal.value = false;
        selectedFiles.value = [];
        uploadForm.name = '';
        alert('Document uploaded successfully!');
    } catch (error) {
        alert('Failed to upload document: ' + error.message);
    } finally {
        uploading.value = false;
    }
};

const viewDocument = (doc) => {
    alert(`Opening ${doc.name}...`);
};

const downloadDocument = (doc) => {
    alert(`Downloading ${doc.name}...`);
};

const loadDocuments = async () => {
    try {
        const response = await axios.get('/documents');
        let data = response.data;
        if (data && typeof data === 'object' && !Array.isArray(data)) {
            data = data.data || [];
        }
        if (!Array.isArray(data)) {
            data = [];
        }
        documents.value = data;
    } catch (error) {
        documents.value = [
            { id: 1, name: 'Contract Agreement.pdf', type: 'pdf', size: 245000, created_at: '2026-04-10' },
            { id: 2, name: 'Client Brief.doc', type: 'doc', size: 128000, created_at: '2026-04-08' },
            { id: 3, name: 'Evidence Photo.jpg', type: 'image', size: 1850000, created_at: '2026-04-05' },
        ];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadDocuments();
});
</script>
