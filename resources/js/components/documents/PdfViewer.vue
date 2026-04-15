<template>
  <div class="pdf-viewer-container">
    <!-- Toolbar -->
    <div class="pdf-toolbar bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-4 flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <!-- Zoom Controls -->
        <div class="flex items-center space-x-2">
          <button
            @click="zoomOut"
            :disabled="scale <= 0.5"
            class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
            title="Zoom Out"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
          </button>

          <select
            v-model="scale"
            @change="setZoom"
            class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          >
            <option :value="0.5">50%</option>
            <option :value="0.75">75%</option>
            <option :value="1">100%</option>
            <option :value="1.25">125%</option>
            <option :value="1.5">150%</option>
            <option :value="2">200%</option>
          </select>

          <button
            @click="zoomIn"
            :disabled="scale >= 2"
            class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
            title="Zoom In"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          </button>
        </div>

        <!-- Page Navigation -->
        <div class="flex items-center space-x-2">
          <button
            @click="previousPage"
            :disabled="currentPage <= 1"
            class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
            title="Previous Page"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <span class="text-sm text-gray-700 dark:text-gray-300">
            Page {{ currentPage }} of {{ totalPages }}
          </span>

          <button
            @click="nextPage"
            :disabled="currentPage >= totalPages"
            class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
            title="Next Page"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <div class="flex items-center space-x-4">
        <!-- Search Toggle -->
        <button
          @click="toggleSearch"
          :class="showSearch ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100'"
          class="p-2 rounded-lg"
          title="Toggle Search"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </button>

        <!-- Fullscreen Toggle -->
        <button
          @click="toggleFullscreen"
          class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          title="Toggle Fullscreen"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
          </svg>
        </button>

        <!-- Download -->
        <button
          @click="downloadPdf"
          class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          title="Download PDF"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Search Panel -->
    <div v-if="showSearch" class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4">
      <div class="flex items-center space-x-4">
        <input
          v-model="searchQuery"
          @keyup.enter="performSearch"
          type="text"
          placeholder="Search in document..."
          class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <button
          @click="performSearch"
          :disabled="!searchQuery.trim()"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Search
        </button>
        <button
          @click="clearSearch"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
        >
          Clear
        </button>
      </div>

      <!-- Search Results -->
      <div v-if="searchResults.length > 0" class="mt-4">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
          Found {{ searchResults.length }} matches
        </div>
        <div class="flex space-x-2 overflow-x-auto">
          <button
            v-for="(result, index) in searchResults"
            :key="index"
            @click="goToSearchResult(result)"
            class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm hover:bg-blue-200 dark:hover:bg-blue-800"
          >
            Page {{ result.page }}, "{{ result.text.substring(0, 20) }}..."
          </button>
        </div>
      </div>
    </div>

    <!-- PDF Viewer -->
    <div class="pdf-viewer flex-1 relative overflow-auto bg-gray-100 dark:bg-gray-900" :class="{ 'fullscreen': isFullscreen }">
      <div v-if="loading" class="flex items-center justify-center h-full">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Loading PDF...</p>
        </div>
      </div>

      <div v-else-if="error" class="flex items-center justify-center h-full">
        <div class="text-center">
          <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <p class="mt-4 text-red-600 dark:text-red-400">{{ error }}</p>
        </div>
      </div>

      <div v-else class="pdf-content" :style="{ transform: `scale(${scale})`, transformOrigin: 'top center' }">
        <canvas
          v-for="(page, index) in pdfPages"
          :key="index"
          :ref="el => setCanvasRef(el, index)"
          :data-page="index + 1"
          class="pdf-page shadow-lg mb-4 mx-auto"
          :class="{ 'search-highlight': hasSearchResults(index + 1) }"
        ></canvas>
      </div>
    </div>

    <!-- Annotations Panel -->
    <div v-if="showAnnotations" class="absolute right-0 top-0 h-full w-80 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 shadow-lg">
      <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Annotations</h3>
      </div>

      <div class="p-4 space-y-4">
        <div v-for="annotation in annotations" :key="annotation.id" class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
          <div class="flex items-start justify-between mb-2">
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ annotation.type }}</span>
            <button @click="deleteAnnotation(annotation.id)" class="text-red-500 hover:text-red-700">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400">{{ annotation.content }}</p>
          <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
            Page {{ annotation.page }}, {{ formatDate(annotation.created_at) }}
          </div>
        </div>

        <!-- Add Annotation -->
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4">
          <textarea
            v-model="newAnnotation"
            placeholder="Add a note..."
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
          ></textarea>
          <button
            @click="addAnnotation"
            :disabled="!newAnnotation.trim()"
            class="mt-2 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Add Note
          </button>
        </div>
      </div>
    </div>

    <!-- Annotation Toggle -->
    <button
      @click="toggleAnnotations"
      :class="showAnnotations ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
      class="absolute right-4 top-4 p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200"
      title="Toggle Annotations"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

// Props
const props = defineProps({
  pdfUrl: {
    type: String,
    required: true
  },
  documentId: {
    type: [String, Number],
    default: null
  }
})

// Emits
const emit = defineEmits(['loaded', 'error', 'page-changed', 'annotation-added', 'annotation-deleted'])

// Reactive data
const loading = ref(true)
const error = ref('')
const pdfDoc = ref(null)
const pdfPages = ref([])
const currentPage = ref(1)
const totalPages = ref(0)
const scale = ref(1.0)
const showSearch = ref(false)
const showAnnotations = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const annotations = ref([])
const newAnnotation = ref('')
const isFullscreen = ref(false)
const canvasRefs = ref([])

// PDF.js loading
let pdfjsLib = null

const loadPdfJs = async () => {
  if (pdfjsLib) return pdfjsLib

  try {
    // In a real implementation, you'd load PDF.js from CDN or bundle it
    // For now, we'll simulate the loading
    pdfjsLib = {
      getDocument: async (url) => {
        // Mock PDF document loading
        return {
          numPages: 10, // Mock 10 pages
          getPage: async (pageNum) => ({
            getViewport: ({ scale }) => ({
              width: 595 * scale, // A4 width
              height: 842 * scale // A4 height
            }),
            render: (context) => {
              // Mock rendering - in real implementation, this would render the actual PDF page
              const canvas = context.canvas;
              const ctx = canvas.getContext('2d');
              ctx.fillStyle = '#f8f9fa';
              ctx.fillRect(0, 0, canvas.width, canvas.height);
              ctx.fillStyle = '#6c757d';
              ctx.font = '24px Arial';
              ctx.textAlign = 'center';
              ctx.fillText(`Page ${pageNum}`, canvas.width / 2, canvas.height / 2);
            }
          })
        }
      }
    }

    return pdfjsLib
  } catch (err) {
    throw new Error('Failed to load PDF.js')
  }
}

const loadDocument = async () => {
  try {
    loading.value = true
    error.value = ''

    const PDFJS = await loadPdfJs()
    pdfDoc.value = await PDFJS.getDocument(props.pdfUrl).promise

    totalPages.value = pdfDoc.value.numPages
    pdfPages.value = Array.from({ length: totalPages.value }, (_, i) => i + 1)

    await nextTick()
    await renderAllPages()

    emit('loaded', { totalPages: totalPages.value })
  } catch (err) {
    error.value = 'Failed to load PDF document'
    emit('error', err)
  } finally {
    loading.value = false
  }
}

const renderAllPages = async () => {
  for (let i = 0; i < totalPages.value; i++) {
    await renderPage(i + 1)
  }
}

const renderPage = async (pageNum) => {
  if (!pdfDoc.value) return

  try {
    const page = await pdfDoc.value.getPage(pageNum)
    const viewport = page.getViewport({ scale: scale.value })

    const canvasRef = canvasRefs.value[pageNum - 1]
    if (!canvasRef) return

    const canvas = canvasRef
    const context = canvas.getContext('2d')

    canvas.height = viewport.height
    canvas.width = viewport.width

    const renderContext = {
      canvasContext: context,
      viewport: viewport
    }

    await page.render(renderContext).promise
  } catch (err) {
    console.error('Error rendering page:', err)
  }
}

const setCanvasRef = (el, index) => {
  if (el) {
    canvasRefs.value[index] = el
  }
}

const setZoom = async () => {
  await renderAllPages()
}

const zoomIn = () => {
  if (scale.value < 2) {
    scale.value += 0.25
    setZoom()
  }
}

const zoomOut = () => {
  if (scale.value > 0.5) {
    scale.value -= 0.25
    setZoom()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    emit('page-changed', currentPage.value)
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    emit('page-changed', currentPage.value)
  }
}

const toggleSearch = () => {
  showSearch.value = !showSearch.value
}

const performSearch = () => {
  if (!searchQuery.value.trim()) return

  // Mock search results - in real implementation, this would search through PDF text content
  searchResults.value = [
    { page: 1, text: 'This is a sample search result' },
    { page: 3, text: 'Another matching result here' },
    { page: 7, text: 'Final search result found' }
  ]
}

const clearSearch = () => {
  searchQuery.value = ''
  searchResults.value = []
}

const goToSearchResult = (result) => {
  currentPage.value = result.page
  emit('page-changed', currentPage.value)
}

const hasSearchResults = (pageNum) => {
  return searchResults.value.some(result => result.page === pageNum)
}

const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value
}

const downloadPdf = () => {
  // In real implementation, trigger download of the PDF file
  const link = document.createElement('a')
  link.href = props.pdfUrl
  link.download = 'document.pdf'
  link.click()
}

const toggleAnnotations = () => {
  showAnnotations.value = !showAnnotations.value
}

const addAnnotation = () => {
  if (!newAnnotation.value.trim()) return

  const annotation = {
    id: Date.now(),
    type: 'Note',
    content: newAnnotation.value,
    page: currentPage.value,
    created_at: new Date(),
    position: { x: 0, y: 0 } // Would be calculated based on click position
  }

  annotations.value.push(annotation)
  newAnnotation.value = ''

  emit('annotation-added', annotation)
}

const deleteAnnotation = (id) => {
  const index = annotations.value.findIndex(a => a.id === id)
  if (index >= -1) {
    annotations.value.splice(index, 1)
    emit('annotation-deleted', id)
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

// Watch for PDF URL changes
watch(() => props.pdfUrl, () => {
  if (props.pdfUrl) {
    loadDocument()
  }
}, { immediate: true })

// Keyboard shortcuts
const handleKeydown = (event) => {
  if (event.ctrlKey || event.metaKey) {
    switch (event.key) {
      case '+':
      case '=':
        event.preventDefault()
        zoomIn()
        break
      case '-':
        event.preventDefault()
        zoomOut()
        break
      case 'f':
        event.preventDefault()
        toggleFullscreen()
        break
      case 's':
        event.preventDefault()
        toggleSearch()
        break
    }
  } else {
    switch (event.key) {
      case 'ArrowLeft':
        event.preventDefault()
        previousPage()
        break
      case 'ArrowRight':
        event.preventDefault()
        nextPage()
        break
    }
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)

  if (props.pdfUrl) {
    loadDocument()
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.pdf-viewer-container {
  @apply h-full flex flex-col;
}

.pdf-viewer {
  @apply flex-1;
}

.pdf-content {
  @apply p-4;
}

.pdf-page {
  @apply border border-gray-300 dark:border-gray-600;
}

.search-highlight {
  @apply ring-2 ring-yellow-400;
}

.fullscreen {
  @apply fixed inset-0 z-50 bg-white dark:bg-gray-900;
}

.fullscreen .pdf-toolbar {
  @apply relative z-10;
}
</style>