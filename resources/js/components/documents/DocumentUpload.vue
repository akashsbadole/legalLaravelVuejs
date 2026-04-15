<template>
  <div class="document-upload">
    <!-- Upload Area -->
    <div
      @dragover.prevent="dragOver = true"
      @dragleave.prevent="dragOver = false"
      @drop.prevent="handleDrop"
      class="relative border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200"
      :class="{
        'border-blue-400 bg-blue-50 dark:bg-blue-900/20': dragOver,
        'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500': !dragOver
      }"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        accept=".pdf,.doc,.docx,.txt,.jpg,.png,.gif"
        @change="handleFileSelect"
        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
      />

      <div class="space-y-4">
        <!-- Upload Icon -->
        <div class="mx-auto w-16 h-16 text-gray-400">
          <svg v-if="!uploading" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
          </svg>
          <div v-else class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
        </div>

        <!-- Upload Text -->
        <div>
          <p class="text-xl font-medium text-gray-900 dark:text-white">
            {{ dragOver ? 'Drop files here' : 'Upload Documents' }}
          </p>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Drag and drop files here, or click to browse
          </p>
        </div>

        <!-- File Types -->
        <div class="flex flex-wrap justify-center gap-2">
          <span
            v-for="type in allowedTypes"
            :key="type"
            class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm"
          >
            {{ type.toUpperCase() }}
          </span>
        </div>

        <!-- Upload Button -->
        <button
          @click="$refs.fileInput.click()"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
        >
          Browse Files
        </button>

        <!-- Max File Size -->
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Maximum file size: {{ formatFileSize(maxFileSize) }}
        </p>
      </div>
    </div>

    <!-- Upload Progress -->
    <div v-if="uploadQueue.length > 0" class="mt-6 space-y-4">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Progress</h3>

      <div
        v-for="file in uploadQueue"
        :key="file.id"
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
      >
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ file.name }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatFileSize(file.size) }}</p>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <span
              :class="getStatusColor(file.status)"
              class="px-2 py-1 text-xs font-medium rounded-full"
            >
              {{ file.status }}
            </span>

            <button
              v-if="file.status === 'error'"
              @click="retryUpload(file)"
              class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              title="Retry upload"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>

            <button
              v-if="canCancel(file)"
              @click="cancelUpload(file)"
              class="p-1 text-gray-400 hover:text-red-600"
              title="Cancel upload"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="file.status === 'uploading'" class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div
            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: file.progress + '%' }"
          ></div>
        </div>

        <!-- Error Message -->
        <div v-if="file.error" class="mt-2 text-sm text-red-600 dark:text-red-400">
          {{ file.error }}
        </div>

        <!-- Processing Steps -->
        <div v-if="file.processingSteps && file.processingSteps.length > 0" class="mt-3 space-y-2">
          <div
            v-for="step in file.processingSteps"
            :key="step.name"
            class="flex items-center space-x-2 text-sm"
          >
            <div
              :class="getStepIconClass(step.status)"
              class="w-4 h-4 flex-shrink-0"
            >
              <svg v-if="step.status === 'completed'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <svg v-else-if="step.status === 'processing'" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <svg v-else-if="step.status === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </div>
            <span :class="getStepTextClass(step.status)">
              {{ step.name }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Settings -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload Settings</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Auto Processing -->
        <div>
          <label class="flex items-center">
            <input
              v-model="autoProcess"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Auto-process files</span>
          </label>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Automatically extract text and analyze content
          </p>
        </div>

        <!-- Create Thumbnails -->
        <div>
          <label class="flex items-center">
            <input
              v-model="createThumbnails"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Generate thumbnails</span>
          </label>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Create preview images for documents
          </p>
        </div>

        <!-- OCR for Images -->
        <div>
          <label class="flex items-center">
            <input
              v-model="enableOCR"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">OCR for images</span>
          </label>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Extract text from scanned documents
          </p>
        </div>

        <!-- Categorize Automatically -->
        <div>
          <label class="flex items-center">
            <input
              v-model="autoCategorize"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Auto-categorize</span>
          </label>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Automatically suggest categories based on content
          </p>
        </div>
      </div>
    </div>

    <!-- Recent Uploads -->
    <div v-if="recentUploads.length > 0" class="mt-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Uploads</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="upload in recentUploads"
          :key="upload.id"
          class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow"
        >
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 dark:text-white truncate">{{ upload.title }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatFileSize(upload.file_size) }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between text-sm">
            <span :class="getStatusColor(upload.processing_status)" class="capitalize">
              {{ upload.processing_status }}
            </span>
            <span class="text-gray-500 dark:text-gray-400">
              {{ formatDate(upload.created_at) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useDocumentStore } from '@/stores/documents'

// Props
const props = defineProps({
  categoryId: {
    type: [String, Number],
    default: null
  },
  maxFiles: {
    type: Number,
    default: 10
  },
  maxFileSize: {
    type: Number,
    default: 10 * 1024 * 1024 // 10MB
  },
  allowedTypes: {
    type: Array,
    default: () => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png']
  }
})

// Emits
const emit = defineEmits(['files-selected', 'upload-completed', 'upload-error'])

// Store
const documentStore = useDocumentStore()

// Reactive data
const dragOver = ref(false)
const uploading = ref(false)
const uploadQueue = ref([])
const autoProcess = ref(true)
const createThumbnails = ref(true)
const enableOCR = ref(true)
const autoCategorize = ref(true)
const recentUploads = ref([])

// File input reference
const fileInput = ref(null)

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  return date.toLocaleDateString()
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900',
    processing: 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900',
    completed: 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900',
    failed: 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900'
  }
  return colors[status] || 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-700'
}

const getStepIconClass = (status) => {
  const classes = {
    pending: 'text-gray-400',
    processing: 'text-blue-500',
    completed: 'text-green-500',
    error: 'text-red-500'
  }
  return classes[status] || 'text-gray-400'
}

const getStepTextClass = (status) => {
  const classes = {
    pending: 'text-gray-500',
    processing: 'text-blue-600',
    completed: 'text-green-600',
    error: 'text-red-600'
  }
  return classes[status] || 'text-gray-500'
}

const canCancel = (file) => {
  return ['pending', 'uploading'].includes(file.status)
}

const validateFile = (file) => {
  // Check file size
  if (file.size > props.maxFileSize) {
    return `File size exceeds ${formatFileSize(props.maxFileSize)} limit`
  }

  // Check file type
  const fileExtension = file.name.split('.').pop().toLowerCase()
  if (!props.allowedTypes.includes(fileExtension)) {
    return `File type .${fileExtension} is not allowed`
  }

  return null
}

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  processFiles(files)
  // Reset input
  fileInput.value.value = ''
}

const handleDrop = (event) => {
  dragOver.value = false
  const files = Array.from(event.dataTransfer.files)
  processFiles(files)
}

const processFiles = (files) => {
  const validFiles = []
  const errors = []

  files.forEach(file => {
    const validationError = validateFile(file)
    if (validationError) {
      errors.push(`${file.name}: ${validationError}`)
    } else {
      validFiles.push(file)
    }
  })

  if (errors.length > 0) {
    emit('upload-error', errors)
  }

  if (validFiles.length > 0) {
    addToUploadQueue(validFiles)
    emit('files-selected', validFiles)
  }
}

const addToUploadQueue = (files) => {
  const queueItems = files.map((file, index) => ({
    id: Date.now() + index,
    file: file,
    name: file.name,
    size: file.size,
    type: file.type,
    status: 'pending',
    progress: 0,
    processingSteps: [
      { name: 'Validating file', status: 'pending' },
      { name: 'Uploading to server', status: 'pending' },
      { name: 'Extracting text', status: 'pending' },
      { name: 'Generating preview', status: 'pending' },
      { name: 'Indexing for search', status: 'pending' }
    ],
    error: null
  }))

  uploadQueue.value.push(...queueItems)
  startUploadProcess()
}

const startUploadProcess = async () => {
  for (const fileItem of uploadQueue.value) {
    if (fileItem.status === 'pending') {
      await uploadFile(fileItem)
    }
  }
}

const uploadFile = async (fileItem) => {
  try {
    fileItem.status = 'uploading'
    fileItem.processingSteps[0].status = 'completed'
    fileItem.processingSteps[1].status = 'processing'

    // Simulate file upload with progress
    const formData = new FormData()
    formData.append('file', fileItem.file)
    formData.append('category_id', props.categoryId || '')
    formData.append('auto_process', autoProcess.value ? '1' : '0')
    formData.append('create_thumbnails', createThumbnails.value ? '1' : '0')
    formData.append('enable_ocr', enableOCR.value ? '1' : '0')

    // In a real implementation, you'd use axios to upload the file
    // For now, we'll simulate the upload process
    for (let progress = 0; progress <= 100; progress += 10) {
      fileItem.progress = progress
      await new Promise(resolve => setTimeout(resolve, 200))
    }

    fileItem.processingSteps[1].status = 'completed'
    fileItem.status = 'processing'

    // Simulate processing steps
    const steps = ['Extracting text', 'Generating preview', 'Indexing for search']
    for (let i = 0; i < steps.length; i++) {
      fileItem.processingSteps[i + 2].status = 'processing'
      await new Promise(resolve => setTimeout(resolve, 1000))
      fileItem.processingSteps[i + 2].status = 'completed'
    }

    fileItem.status = 'completed'

    // Add to recent uploads
    recentUploads.value.unshift({
      id: fileItem.id,
      title: fileItem.name,
      file_size: fileItem.size,
      processing_status: 'completed',
      created_at: new Date().toISOString()
    })

    emit('upload-completed', fileItem)

  } catch (error) {
    fileItem.status = 'error'
    fileItem.error = error.message || 'Upload failed'
    emit('upload-error', fileItem.error)
  }
}

const retryUpload = (fileItem) => {
  fileItem.status = 'pending'
  fileItem.error = null
  fileItem.progress = 0
  fileItem.processingSteps.forEach(step => {
    step.status = 'pending'
  })

  uploadFile(fileItem)
}

const cancelUpload = (fileItem) => {
  fileItem.status = 'cancelled'
  // In real implementation, you'd abort the upload request
}

// Cleanup completed uploads after some time
setInterval(() => {
  uploadQueue.value = uploadQueue.value.filter(item =>
    item.status !== 'completed' || (Date.now() - item.id) < 30000 // Keep for 30 seconds
  )
}, 5000)
</script>