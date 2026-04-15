<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
    <!-- Header with file icon and type badge -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="getFileIconClass(document.file_format)">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path :d="getFileIconPath(document.file_format)" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate max-w-32" :title="document.title">
              {{ document.title }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              {{ formatFileSize(document.file_size) }}
            </p>
          </div>
        </div>

        <span :class="getStatusBadgeClass(document.processing_status)" class="px-2 py-1 text-xs font-medium rounded-full">
          {{ getStatusLabel(document.processing_status) }}
        </span>
      </div>

      <!-- Document type and category -->
      <div class="flex items-center justify-between text-xs">
        <span class="text-gray-600 dark:text-gray-400">
          {{ getDocumentTypeLabel(document.document_type) }}
        </span>
        <span v-if="document.category" class="text-blue-600 dark:text-blue-400 font-medium">
          {{ document.category.name }}
        </span>
      </div>
    </div>

    <!-- Preview/Thumbnail area -->
    <div class="h-32 bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
      <div v-if="document.processing_status === 'completed'" class="text-center">
        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-xs text-gray-500 dark:text-gray-400">Preview available</p>
      </div>
      <div v-else-if="document.processing_status === 'processing'" class="text-center">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
        <p class="text-xs text-gray-500 dark:text-gray-400">Processing...</p>
      </div>
      <div v-else class="text-center">
        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <p class="text-xs text-red-500">{{ getStatusLabel(document.processing_status) }}</p>
      </div>
    </div>

    <!-- Action buttons -->
    <div class="p-4">
      <div class="flex gap-2 mb-3">
        <button
          @click="$emit('view', document)"
          class="flex-1 px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors"
        >
          View
        </button>
        <button
          @click="$emit('download', document)"
          class="flex-1 px-3 py-2 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition-colors"
        >
          Download
        </button>
      </div>

      <!-- Metadata -->
      <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
        <div class="flex justify-between">
          <span>Uploaded:</span>
          <span>{{ formatDate(document.created_at) }}</span>
        </div>
        <div class="flex justify-between">
          <span>Downloads:</span>
          <span>{{ document.download_count }}</span>
        </div>
        <div class="flex justify-between">
          <span>Views:</span>
          <span>{{ document.view_count }}</span>
        </div>
      </div>

      <!-- Menu button -->
      <div class="relative mt-3">
        <button
          @click="toggleMenu"
          class="w-full p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
          </svg>
        </button>

        <!-- Dropdown menu -->
        <div
          v-if="showMenu"
          class="absolute bottom-full left-0 mb-2 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10"
        >
          <div class="py-1">
            <button
              @click="editDocument"
              class="w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit
            </button>

            <button
              @click="duplicateDocument"
              class="w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              Duplicate
            </button>

            <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>

            <button
              @click="confirmDelete"
              class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete
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

const emit = defineEmits(['view', 'download', 'edit', 'delete'])

const showMenu = ref(false)

const getFileIconClass = (format) => {
  const classes = {
    pdf: 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400',
    doc: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400',
    docx: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400',
    txt: 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-400',
    json: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400',
    xml: 'bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400',
    csv: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400',
    jpg: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400',
    png: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400',
    gif: 'bg-pink-100 text-pink-600 dark:bg-pink-900 dark:text-pink-400',
    mp4: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400',
    avi: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400',
    zip: 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-400',
    rar: 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-400'
  }
  return classes[format] || 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-400'
}

const getFileIconPath = (format) => {
  const paths = {
    pdf: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    doc: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    docx: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    txt: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    json: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    xml: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    csv: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6M16 13H8m8 3H8m4-6H8',
    jpg: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    png: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    gif: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    mp4: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    avi: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    zip: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
    rar: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
  }
  return paths[format] || paths.txt
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    processing: 'Processing',
    completed: 'Ready',
    failed: 'Failed'
  }
  return labels[status] || status.charAt(0).toUpperCase() + status.slice(1)
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    training_data: 'Training',
    conversation_log: 'Logs',
    knowledge_base: 'Knowledge',
    configuration: 'Config',
    api_documentation: 'API Docs',
    user_guide: 'Guide',
    system_manual: 'Manual',
    analytics_report: 'Analytics',
    error_log: 'Errors',
    performance_metrics: 'Metrics',
    conversation_flow: 'Flow',
    decision_tree: 'Decisions',
    fallback_response: 'Fallback',
    custom_script: 'Script',
    integration_guide: 'Integration'
  }
  return labels[type] || type.replace(/_/g, ' ').toUpperCase()
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays}d ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`
  return date.toLocaleDateString()
}

const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const editDocument = () => {
  showMenu.value = false
  emit('edit', props.document)
}

const duplicateDocument = () => {
  showMenu.value = false
  // Implement duplicate functionality
  console.log('Duplicate document:', props.document.id)
}

const confirmDelete = () => {
  showMenu.value = false
  emit('delete', props.document)
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  const menu = event.target.closest('.relative')
  if (!menu) {
    showMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>