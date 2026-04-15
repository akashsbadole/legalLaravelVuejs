<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl" :style="{ backgroundColor: source.color + '20', color: source.color }">
            {{ source.icon }}
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ source.name }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ items.length }} sections available</p>
          </div>
        </div>

        <button
          @click="$emit('close')"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 max-h-96 overflow-y-auto">
        <!-- Search/Filter -->
        <div class="mb-6">
          <input
            v-model="searchFilter"
            type="text"
            placeholder="Search sections..."
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Items List -->
        <div class="space-y-3">
          <LegalItemCard
            v-for="item in filteredItems"
            :key="getItemKey(item)"
            :item="item"
            :source-type="source.slug"
            @view-details="$emit('view-item', item)"
            @add-to-chatbot="$emit('add-to-chatbot', item)"
          />
        </div>

        <!-- Empty State -->
        <div v-if="filteredItems.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No sections found</h3>
          <p class="text-gray-600 dark:text-gray-400">Try adjusting your search terms</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-600 dark:text-gray-400">
          Showing {{ filteredItems.length }} of {{ items.length }} sections
        </div>
        <div class="flex gap-3">
          <button
            @click="$emit('close')"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Close
          </button>
          <button
            @click="addAllToChatbot"
            :disabled="filteredItems.length === 0"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Add All to Chatbot
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import LegalItemCard from '@/components/legal/LegalItemCard.vue'

const props = defineProps({
  source: {
    type: Object,
    required: true
  },
  items: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close', 'view-item', 'add-to-chatbot'])

const searchFilter = ref('')

const filteredItems = computed(() => {
  if (!searchFilter.value) return props.items

  const filter = searchFilter.value.toLowerCase()
  return props.items.filter(item => {
    return (item.title && item.title.toLowerCase().includes(filter)) ||
           (item.description && item.description.toLowerCase().includes(filter)) ||
           (item.section_number && item.section_number.toString().includes(filter)) ||
           (item.article_number && item.article_number.toString().includes(filter))
  })
})

const getItemKey = (item) => {
  return item.id || item.section_number || item.article_number || Math.random()
}

const addAllToChatbot = () => {
  filteredItems.value.forEach(item => {
    emit('add-to-chatbot', item)
  })
}
</script>