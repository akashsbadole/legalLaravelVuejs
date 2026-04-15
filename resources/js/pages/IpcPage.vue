<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Indian Penal Code (IPC)</h1>
        <p class="text-gray-600">Browse sections of the Indian Penal Code with classifications and penalties</p>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search sections..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div>
            <select
              v-model="classificationFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Classifications</option>
              <option value="bailable">Bailable</option>
              <option value="non-bailable">Non-Bailable</option>
              <option value="compoundable">Compoundable</option>
              <option value="non-compoundable">Non-Compoundable</option>
            </select>
          </div>
          <div>
            <select
              v-model="cognizableFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Types</option>
              <option value="cognizable">Cognizable</option>
              <option value="non-cognizable">Non-Cognizable</option>
            </select>
          </div>
          <div class="flex gap-2">
            <button
              @click="loadSections"
              :disabled="loading"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              <span v-if="loading">Loading...</span>
              <span v-else>Search</span>
            </button>
            <button
              @click="clearFilters"
              class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
            >
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Sections</p>
              <p class="text-2xl font-semibold text-gray-900">{{ sections.length }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Bailable</p>
              <p class="text-2xl font-semibold text-gray-900">{{ bailableCount }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Cognizable</p>
              <p class="text-2xl font-semibold text-gray-900">{{ cognizableCount }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active</p>
              <p class="text-2xl font-semibold text-gray-900">{{ activeCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Sections List -->
      <div class="space-y-4">
        <IpcSectionCard
          v-for="section in filteredSections"
          :key="section.id"
          :section="section"
          @view-details="viewSectionDetails"
        />
      </div>

      <!-- Loading State -->
      <div v-if="loading && sections.length === 0" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading IPC sections...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && sections.length === 0" class="text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No IPC sections found</h3>
        <p class="text-gray-600">Try adjusting your search criteria</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLegalStore } from '@/stores/legal'
import AppLayout from '@/layouts/AppLayout.vue'
import IpcSectionCard from '@/components/legal/IpcSectionCard.vue'

const legalStore = useLegalStore()

const sections = computed(() => legalStore.ipcSections)
const loading = computed(() => legalStore.loading)

const searchQuery = ref('')
const classificationFilter = ref('')
const cognizableFilter = ref('')

const bailableCount = computed(() =>
  sections.value.filter(section => section.classification === 'bailable').length
)

const cognizableCount = computed(() =>
  sections.value.filter(section => section.cognizable === 'cognizable').length
)

const activeCount = computed(() =>
  sections.value.filter(section => section.is_active).length
)

const filteredSections = computed(() => {
  let filtered = sections.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(section =>
      section.title.toLowerCase().includes(query) ||
      section.description.toLowerCase().includes(query) ||
      section.section_number.toString().includes(query)
    )
  }

  if (classificationFilter.value) {
    filtered = filtered.filter(section => section.classification === classificationFilter.value)
  }

  if (cognizableFilter.value) {
    filtered = filtered.filter(section => section.cognizable === cognizableFilter.value)
  }

  return filtered
})

const loadSections = async () => {
  const params = {}
  if (searchQuery.value) params.search = searchQuery.value
  if (classificationFilter.value) params.classification = classificationFilter.value
  if (cognizableFilter.value) params.cognizable = cognizableFilter.value

  await legalStore.loadIpcSections(params)
}

const clearFilters = () => {
  searchQuery.value = ''
  classificationFilter.value = ''
  cognizableFilter.value = ''
  loadSections()
}

const viewSectionDetails = (section) => {
  // Navigate to section details
  console.log('View IPC section details:', section)
}

onMounted(() => {
  if (sections.value.length === 0) {
    loadSections()
  }
})
</script>