<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Indian Legal Database</h1>
        <p class="text-gray-600">Search and explore Indian Penal Code, Criminal Procedure Code, Civil Procedure Code, and Constitution</p>
      </div>

      <!-- Search Bar -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              @keyup.enter="performSearch"
              type="text"
              placeholder="Search legal sections, articles, or keywords..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select
            v-model="searchType"
            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="all">All Legal Sources</option>
            <option value="ipc">Indian Penal Code</option>
            <option value="crpc">Criminal Procedure Code</option>
            <option value="cpc">Civil Procedure Code</option>
            <option value="constitution">Constitution</option>
          </select>
          <button
            @click="performSearch"
            :disabled="loading"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>Search</span>
          </button>
        </div>

        <!-- Quick Filters -->
        <div class="mt-4 flex flex-wrap gap-2">
          <button
            v-for="filter in quickFilters"
            :key="filter.key"
            @click="applyQuickFilter(filter)"
            class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
          >
            {{ filter.label }}
          </button>
        </div>
      </div>

      <!-- Results Section -->
      <div v-if="results.length > 0" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">
            Search Results ({{ totalResults }})
          </h2>
          <div class="flex gap-2">
            <select
              v-model="sortBy"
              @change="sortResults"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
            >
              <option value="relevance">Sort by Relevance</option>
              <option value="title">Sort by Title</option>
              <option value="type">Sort by Type</option>
            </select>
          </div>
        </div>

        <!-- Results List -->
        <div class="space-y-4">
          <LegalResultCard
            v-for="result in paginatedResults"
            :key="result.id"
            :result="result"
            @view-details="viewDetails"
          />
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center mt-8">
          <nav class="flex items-center gap-2">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
            >
              Previous
            </button>

            <span class="px-4 py-2 text-gray-700">
              Page {{ currentPage }} of {{ totalPages }}
            </span>

            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
            >
              Next
            </button>
          </nav>
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="searched && !loading" class="text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
        <p class="text-gray-600">Try adjusting your search terms or filters</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Searching legal database...</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLegalStore } from '@/stores/legal'
import AppLayout from '@/layouts/AppLayout.vue'
import LegalResultCard from '@/components/legal/LegalResultCard.vue'

const legalStore = useLegalStore()

const searchQuery = ref('')
const searchType = ref('all')
const loading = ref(false)
const searched = ref(false)
const sortBy = ref('relevance')
const currentPage = ref(1)
const resultsPerPage = 10

const results = computed(() => legalStore.searchResults)
const totalResults = computed(() => legalStore.totalResults)

const quickFilters = [
  { key: 'murder', label: 'Murder Cases', type: 'ipc' },
  { key: 'property', label: 'Property Disputes', type: 'cpc' },
  { key: 'fundamental rights', label: 'Fundamental Rights', type: 'constitution' },
  { key: 'arrest', label: 'Arrest Procedures', type: 'crpc' },
  { key: 'bailable', label: 'Bailable Offenses', type: 'ipc' }
]

const totalPages = computed(() => Math.ceil(results.value.length / resultsPerPage))

const paginatedResults = computed(() => {
  const start = (currentPage.value - 1) * resultsPerPage
  const end = start + resultsPerPage
  return results.value.slice(start, end)
})

const performSearch = async () => {
  if (!searchQuery.value.trim()) return

  loading.value = true
  searched.value = true

  try {
    await legalStore.searchLegalDatabase({
      q: searchQuery.value,
      type: searchType.value,
      limit: 50
    })
    currentPage.value = 1
  } catch (error) {
    console.error('Search failed:', error)
  } finally {
    loading.value = false
  }
}

const applyQuickFilter = (filter) => {
  searchQuery.value = filter.key
  searchType.value = filter.type
  performSearch()
}

const sortResults = () => {
  // Sorting logic would be implemented in the store
  legalStore.sortResults(sortBy.value)
}

const viewDetails = (result) => {
  // Navigate to detailed view
  console.log('View details for:', result)
}

onMounted(() => {
  // Load initial data or popular searches
})
</script>