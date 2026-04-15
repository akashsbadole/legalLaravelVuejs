<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer group"
    @click="$emit('browse', source)"
  >
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl" :style="{ backgroundColor: source.color + '20', color: source.color }">
        {{ source.icon }}
      </div>
      <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </div>

    <!-- Content -->
    <div class="mb-4">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
        {{ source.name }}
      </h3>
      <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
        {{ source.description }}
      </p>
      <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
        <span>{{ source.itemCount }} sections</span>
        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
          {{ source.shortName }}
        </span>
      </div>
    </div>

    <!-- Action Button -->
    <button
      class="w-full py-3 px-4 rounded-lg font-medium text-white transition-all duration-300 transform hover:scale-105"
      :style="{ backgroundColor: source.color }"
    >
      Browse {{ source.shortName }}
    </button>

    <!-- Hover effect -->
    <div class="absolute inset-0 rounded-xl border-2 border-transparent group-hover:border-blue-300 dark:group-hover:border-blue-600 transition-colors duration-300 pointer-events-none"></div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  source: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['browse'])
</script>