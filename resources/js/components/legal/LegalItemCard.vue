<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <!-- Title and Section Number -->
        <div class="flex items-center gap-2 mb-2">
          <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
            {{ item.title }}
          </h4>
          <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
            {{ getSectionLabel(item) }}
          </span>
        </div>

        <!-- Description -->
        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
          {{ item.description }}
        </p>

        <!-- Metadata -->
        <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
          <span v-if="item.classification" class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded">
            {{ item.classification }}
          </span>
          <span v-if="item.cognizable" class="bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded">
            {{ item.cognizable ? 'Cognizable' : 'Non-cognizable' }}
          </span>
          <span v-if="item.court_level" class="bg-purple-50 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-2 py-1 rounded">
            {{ item.court_level }}
          </span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 ml-4">
        <button
          @click="$emit('view-details', item)"
          class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
          title="View Details"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </button>
        <button
          @click="$emit('add-to-chatbot', item)"
          class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
          title="Add to Chatbot Knowledge"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  sourceType: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['view-details', 'add-to-chatbot'])

const getSectionLabel = (item) => {
  if (item.section_number) return `Section ${item.section_number}`
  if (item.article_number) return `Article ${item.article_number}`
  return 'N/A'
}
</script>