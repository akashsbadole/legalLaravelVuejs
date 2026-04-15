<template>
  <div class="legal-database-search">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Indian Legal Database</h2>
          <p class="text-gray-600 dark:text-gray-400">Search and explore IPC, CrPC, CPC, and Constitution</p>
        </div>

        <!-- Toggle View -->
        <div class="flex items-center gap-2">
          <button
            @click="viewMode = 'search'"
            :class="viewMode === 'search' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          >
            Search
          </button>
          <button
            @click="viewMode = 'browse'"
            :class="viewMode === 'browse' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          >
            Browse
          </button>
        </div>
      </div>
    </div>

    <!-- Search View -->
    <div v-if="viewMode === 'search'" class="space-y-6">
      <!-- Search Interface -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <input
              v-model="searchQuery"
              @keyup.enter="performLegalSearch"
              type="text"
              placeholder="Search legal sections, articles, or keywords..."
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <select
              v-model="selectedLaw"
              @change="performLegalSearch"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="all">All Legal Sources</option>
              <option value="ipc">Indian Penal Code</option>
              <option value="crpc">Criminal Procedure Code</option>
              <option value="cpc">Civil Procedure Code</option>
              <option value="constitution">Constitution</option>
            </select>
          </div>

          <div class="flex gap-2">
            <button
              @click="performLegalSearch"
              :disabled="loading"
              class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">Searching...</span>
              <span v-else>Search</span>
            </button>
            <button
              @click="clearSearch"
              class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
            >
              Clear
            </button>
          </div>
        </div>

        <!-- Quick Search Terms -->
        <div class="mt-4 flex flex-wrap gap-2">
          <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Quick search:</span>
          <button
            v-for="term in quickSearchTerms"
            :key="term"
            @click="quickSearch(term)"
            class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            {{ term }}
          </button>
        </div>
      </div>

      <!-- Search Results -->
      <div v-if="legalSearchResults.length > 0" class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Search Results ({{ legalSearchResults.length }})
          </h3>
          <button
            @click="exportResults"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
          >
            Export Results
          </button>
        </div>

        <div class="space-y-3">
          <LegalResultCard
            v-for="result in legalSearchResults"
            :key="result.id"
            :result="result"
            @view-details="viewLegalDetails"
            @add-to-chatbot="addToChatbot"
          />
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="searched && !loading" class="text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No results found</h3>
        <p class="text-gray-600 dark:text-gray-400">Try adjusting your search terms or select a different legal source</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Searching legal database...</p>
      </div>
    </div>

    <!-- Browse View -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <LegalSourceCard
        v-for="source in legalSources"
        :key="source.slug"
        :source="source"
        @browse="browseLegalSource"
      />
    </div>

    <!-- Legal Source Details Modal -->
    <LegalSourceModal
      v-if="selectedSource"
      :source="selectedSource"
      :items="sourceItems"
      @close="selectedSource = null"
      @view-item="viewLegalDetails"
      @add-to-chatbot="addToChatbot"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import LegalResultCard from '@/components/legal/LegalResultCard.vue'
import LegalSourceCard from '@/components/legal/LegalSourceCard.vue'
import LegalSourceModal from '@/components/legal/LegalSourceModal.vue'

// Emits
const emit = defineEmits(['add-to-knowledge-base'])

// Reactive data
const viewMode = ref('search')
const searchQuery = ref('')
const selectedLaw = ref('all')
const loading = ref(false)
const searched = ref(false)
const selectedSource = ref(null)
const sourceItems = ref([])

// Legal sources data
const legalSources = ref([
  {
    slug: 'ipc',
    name: 'Indian Penal Code',
    shortName: 'IPC',
    description: 'Criminal law sections and punishments',
    icon: '⚖️',
    color: '#DC2626',
    itemCount: 13,
    route: '/legal/ipc'
  },
  {
    slug: 'crpc',
    name: 'Criminal Procedure Code',
    shortName: 'CrPC',
    description: 'Criminal procedure and court processes',
    icon: '🏛️',
    color: '#EA580C',
    itemCount: 12,
    route: '/legal/crpc'
  },
  {
    slug: 'cpc',
    name: 'Civil Procedure Code',
    shortName: 'CPC',
    description: 'Civil litigation procedures and rules',
    icon: '📋',
    color: '#2563EB',
    itemCount: 10,
    route: '/legal/cpc'
  },
  {
    slug: 'constitution',
    name: 'Constitution of India',
    shortName: 'Constitution',
    description: 'Fundamental rights and constitutional provisions',
    icon: '🇮🇳',
    color: '#16A34A',
    itemCount: 17,
    route: '/legal/constitution'
  }
])

// Quick search terms
const quickSearchTerms = [
  'murder', 'theft', 'rape', 'assault', 'fundamental rights',
  'due process', 'arrest', 'bail', 'appeal', 'evidence'
]

// Search results
const legalSearchResults = ref([])

// Computed properties
const currentSource = computed(() => {
  return legalSources.value.find(source => source.slug === selectedSource.value)
})

// Methods
const performLegalSearch = async () => {
  if (!searchQuery.value.trim()) return

  loading.value = true
  searched.value = true

  try {
    const response = await axios.get('/api/legal/search', {
      params: {
        q: searchQuery.value,
        type: selectedLaw.value === 'all' ? 'all' : selectedLaw.value,
        limit: 50
      }
    })

    legalSearchResults.value = response.data.results || []
  } catch (error) {
    console.error('Legal search failed:', error)
    legalSearchResults.value = []

    // Show user-friendly error message
    if (error.response) {
      // Server responded with error status
      alert(`Search failed: ${error.response.data.message || 'Server error'}`)
    } else if (error.request) {
      // Network error
      alert('Search failed: Network error. Please check your connection.')
    } else {
      // Other error
      alert('Search failed: An unexpected error occurred.')
    }
  } finally {
    loading.value = false
  }
}

const quickSearch = (term) => {
  searchQuery.value = term
  selectedLaw.value = 'all'
  performLegalSearch()
}

const clearSearch = () => {
  searchQuery.value = ''
  selectedLaw.value = 'all'
  legalSearchResults.value = []
  searched.value = false
}

const browseLegalSource = async (source) => {
  selectedSource.value = source.slug

  try {
    let endpoint = ''
    switch (source.slug) {
      case 'ipc':
        endpoint = '/api/legal/ipc'
        break
      case 'crpc':
        endpoint = '/api/legal/crpc'
        break
      case 'cpc':
        endpoint = '/api/legal/cpc'
        break
      case 'constitution':
        endpoint = '/api/legal/constitution'
        break
    }

    if (endpoint) {
      const response = await axios.get(endpoint)
      sourceItems.value = response.data.data || []
    }
  } catch (error) {
    console.error('Failed to load source items:', error)
    sourceItems.value = []
    alert(`Failed to load ${source.name} sections. Please try again.`)
  }
}

const viewLegalDetails = (item) => {
  // Navigate to detailed view or open modal
  console.log('View legal details:', item)
}

const addToChatbot = (item) => {
  // Add legal information to chatbot knowledge base
  emit('add-to-knowledge-base', {
    type: 'legal_reference',
    content: item,
    source: item.type || 'legal'
  })
}

const exportResults = () => {
  // Export search results as PDF or CSV
  const dataStr = JSON.stringify(legalSearchResults.value, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })

  const link = document.createElement('a')
  link.href = URL.createObjectURL(dataBlob)
  link.download = 'legal_search_results.json'
  link.click()
}

onMounted(() => {
  // Initialize component
})
</script>