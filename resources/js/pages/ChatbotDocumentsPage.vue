<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Chatbot Document Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage all documents related to your AI chatbot system</p>
          </div>

          <div class="flex gap-4">
            <!-- Theme Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
              title="Toggle Dark Mode"
            >
              <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
              </svg>
              <svg v-else class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
              </svg>
            </button>

            <!-- Upload Button -->
            <button
              @click="showUploadModal = true"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Upload Documents
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Documents</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total_documents || 0 }}</p>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Processing</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.processing || 0 }}</p>
            </div>
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Storage Used</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatBytes(stats.storage_used || 0) }}</p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Categories</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ categories.length }}</p>
            </div>
            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <div class="md:col-span-2">
            <input
              v-model="searchQuery"
              @keyup.enter="performSearch"
              type="text"
              placeholder="Search documents..."
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <select
              v-model="selectedType"
              @change="performSearch"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Types</option>
              <option value="training_data">Training Data</option>
              <option value="conversation_log">Conversation Logs</option>
              <option value="knowledge_base">Knowledge Base</option>
              <option value="configuration">Configuration</option>
              <option value="api_documentation">API Docs</option>
              <option value="analytics_report">Analytics</option>
            </select>
          </div>

          <div>
            <select
              v-model="selectedCategory"
              @change="performSearch"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="flex gap-2">
            <button
              @click="performSearch"
              :disabled="loading"
              class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">Searching...</span>
              <span v-else>Search</span>
            </button>
            <button
              @click="clearFilters"
              class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
            >
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Documents Grid -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Documents ({{ totalResults }})
          </h2>
          <div class="flex gap-2">
            <select
              v-model="sortBy"
              @change="performSearch"
              class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            >
              <option value="created_at">Newest First</option>
              <option value="title">Title A-Z</option>
              <option value="file_size">Size</option>
              <option value="download_count">Most Downloaded</option>
            </select>
          </div>
        </div>

        <div v-if="loading && documents.length === 0" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Loading documents...</p>
        </div>

        <div v-else-if="documents.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No documents found</h3>
          <p class="text-gray-600 dark:text-gray-400">Upload your first document to get started</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <ChatbotDocumentCard
            v-for="document in documents"
            :key="document.id"
            :document="document"
            @view="viewDocument"
            @download="downloadDocument"
            @edit="editDocument"
            @delete="deleteDocument"
          />
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center mt-8">
          <nav class="flex items-center gap-2">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>

            <span class="px-4 py-2 text-gray-700 dark:text-gray-300">
              Page {{ currentPage }} of {{ totalPages }}
            </span>

            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </nav>
        </div>
      </div>

      <!-- Legal Database Search Section -->
      <div class="mb-8">
        <LegalDatabaseSearch @add-to-knowledge-base="addLegalReferenceToKnowledge" />
      </div>

      <!-- Upload Modal -->
      <DocumentUpload
        v-if="showUploadModal"
        @close="showUploadModal = false"
        @upload-completed="onUploadCompleted"
        @upload-error="onUploadError"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useChatbotDocumentStore } from '@/stores/chatbotDocuments'
import AppLayout from '@/layouts/AppLayout.vue'
import ChatbotDocumentCard from '@/components/chatbot/ChatbotDocumentCard.vue'
import DocumentUpload from '@/components/documents/DocumentUpload.vue'
import LegalDatabaseSearch from '@/components/chatbot/LegalDatabaseSearch.vue'

const documentStore = useChatbotDocumentStore()

const isDarkMode = ref(false)
const showUploadModal = ref(false)
const searchQuery = ref('')
const selectedType = ref('')
const selectedCategory = ref('')
const sortBy = ref('created_at')
const currentPage = ref(1)
const loading = ref(false)

const documents = computed(() => documentStore.documents)
const categories = computed(() => documentStore.categories)
const stats = computed(() => documentStore.stats)
const totalResults = computed(() => documentStore.totalResults)
const totalPages = computed(() => documentStore.totalPages)

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}

const performSearch = async () => {
  loading.value = true

  try {
    await documentStore.searchDocuments({
      q: searchQuery.value,
      type: selectedType.value,
      category: selectedCategory.value,
      sort_by: sortBy.value,
      page: currentPage.value
    })
  } catch (error) {
    console.error('Search failed:', error)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedType.value = ''
  selectedCategory.value = ''
  sortBy.value = 'created_at'
  currentPage.value = 1
  performSearch()
}

const viewDocument = (document) => {
  // Navigate to document viewer
  console.log('View document:', document)
}

const downloadDocument = (document) => {
  // Trigger download
  console.log('Download document:', document)
}

const editDocument = (document) => {
  // Open edit modal or navigate to edit page
  console.log('Edit document:', document)
}

const deleteDocument = async (document) => {
  if (confirm(`Are you sure you want to delete "${document.title}"?`)) {
    try {
      await documentStore.deleteDocument(document.id)
      performSearch() // Refresh the list
    } catch (error) {
      console.error('Delete failed:', error)
    }
  }
}

const onUploadCompleted = (file) => {
  showUploadModal.value = false
  performSearch() // Refresh the list
}

const onUploadError = (error) => {
  console.error('Upload error:', error)
  // Show error notification
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const addLegalReferenceToKnowledge = async (legalReference) => {
  try {
    // Create a new document entry for the legal reference
    const documentData = {
      title: `${legalReference.content.title} - Legal Reference`,
      description: legalReference.content.description || 'Legal reference from Indian legal database',
      content: JSON.stringify(legalReference.content),
      type: 'knowledge_base',
      category_id: null, // We'll need to create or find a legal references category
      tags: [`legal-${legalReference.source}`, legalReference.content.type],
      is_legal_reference: true,
      original_source: legalReference.source
    }

    await documentStore.createDocument(documentData)
    performSearch() // Refresh the list to show the new legal reference

    // Show success notification
    alert('Legal reference added to knowledge base successfully!')
  } catch (error) {
    console.error('Failed to add legal reference:', error)
    alert('Failed to add legal reference. Please try again.')
  }
}

// Watch for page changes
watch(currentPage, () => {
  performSearch()
})

onMounted(async () => {
  await documentStore.loadCategories()
  await documentStore.loadStats()
  performSearch()
})
</script>