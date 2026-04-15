<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <!-- Document Title -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ document.title }}</h3>

        <!-- Document Type Badge -->
        <span :class="getTypeBadgeClass(document.type)" class="px-2 py-1 text-xs font-medium rounded-full mb-3 inline-block">
          {{ getTypeLabel(document.type) }}
        </span>

        <!-- Status and Progress -->
        <div class="flex items-center gap-4 mb-3">
          <div class="flex items-center gap-2">
            <div :class="getStatusDotClass(document.status)" class="w-2 h-2 rounded-full"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ document.status }}</span>
          </div>

          <div class="flex items-center gap-2">
            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="h-2 rounded-full transition-all duration-300"
                :class="getProgressBarClass(document.completion_percentage)"
                :style="{ width: document.completion_percentage + '%' }"
              ></div>
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ document.completion_percentage }}%</span>
          </div>
        </div>

        <!-- Created Date -->
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Created {{ formatDate(document.created_at) }}
        </p>
      </div>

      <!-- Actions Menu -->
      <div class="relative">
        <button
          @click="toggleMenu"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div
          v-if="showMenu"
          class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10"
        >
          <div class="py-1">
            <button
              @click="viewDocument"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              View Document
            </button>

            <button
              @click="editDocument"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit Document
            </button>

            <button
              @click="duplicateDocument"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              Duplicate
            </button>

            <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>

            <button
              @click="deleteDocument"
              class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete Document
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  document: {
    type: Object,
    required: true
  }
})

const showMenu = ref(false)

const getTypeBadgeClass = (type) => {
  const classes = {
    nda: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    employment_contract: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    service_agreement: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    lease_agreement: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    power_of_attorney: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    legal_notice: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    court_petition: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    lease_deed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    sale_deed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    partnership_deed: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    promissory_note: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    legal_letter: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
    affidavit: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300'
  }
  return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

const getTypeLabel = (type) => {
  const labels = {
    nda: 'NDA',
    employment_contract: 'Employment',
    service_agreement: 'Service',
    lease_agreement: 'Lease',
    power_of_attorney: 'POA',
    legal_notice: 'Notice',
    court_petition: 'Petition',
    lease_deed: 'Lease Deed',
    sale_deed: 'Sale Deed',
    partnership_deed: 'Partnership',
    promissory_note: 'Promissory',
    legal_letter: 'Letter',
    affidavit: 'Affidavit'
  }
  return labels[type] || type.replace(/_/g, ' ').toUpperCase()
}

const getStatusDotClass = (status) => {
  const classes = {
    draft: 'bg-gray-400',
    review: 'bg-yellow-400',
    approved: 'bg-blue-400',
    signed: 'bg-purple-400',
    completed: 'bg-green-400',
    archived: 'bg-gray-500'
  }
  return classes[status] || 'bg-gray-400'
}

const getProgressBarClass = (percentage) => {
  if (percentage >= 80) return 'bg-green-500'
  if (percentage >= 60) return 'bg-yellow-500'
  if (percentage >= 40) return 'bg-orange-500'
  return 'bg-red-500'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 1) return 'today'
  if (diffDays === 2) return 'yesterday'
  if (diffDays <= 7) return `${diffDays} days ago`
  if (diffDays <= 30) return `${Math.ceil(diffDays / 7)} weeks ago`
  return date.toLocaleDateString()
}

const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const closeMenu = () => {
  showMenu.value = false
}

const viewDocument = () => {
  closeMenu()
  // Navigate to document view
  console.log('View document:', props.document.id)
}

const editDocument = () => {
  closeMenu()
  // Navigate to document edit
  console.log('Edit document:', props.document.id)
}

const duplicateDocument = () => {
  closeMenu()
  // Duplicate document
  console.log('Duplicate document:', props.document.id)
}

const deleteDocument = () => {
  closeMenu()
  if (confirm('Are you sure you want to delete this document?')) {
    // Delete document
    console.log('Delete document:', props.document.id)
  }
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  const menu = event.target.closest('.relative')
  if (!menu) {
    closeMenu()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>