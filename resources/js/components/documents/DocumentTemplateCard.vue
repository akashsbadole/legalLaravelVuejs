<template>
  <div
    class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer"
    @click="$emit('select-template', template)"
  >
    <!-- Background gradient effect -->
    <div
      class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-5 transition-opacity duration-300"
      :style="{ backgroundColor: template.color }"
    ></div>

    <!-- Header -->
    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4">
        <div
          class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl"
          :style="{ backgroundColor: template.color + '20', color: template.color }"
        >
          {{ template.icon }}
        </div>
        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </div>
      </div>

      <!-- Title and Description -->
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
          {{ template.name }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
          {{ template.description }}
        </p>
      </div>

      <!-- Features/Placeholders -->
      <div class="mb-4">
        <div class="flex flex-wrap gap-1">
          <span
            v-for="placeholder in template.placeholders.slice(0, 3)"
            :key="placeholder"
            class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
          >
            {{ placeholder.replace(/_/g, ' ') }}
          </span>
          <span
            v-if="template.placeholders.length > 3"
            class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
          >
            +{{ template.placeholders.length - 3 }} more
          </span>
        </div>
      </div>

      <!-- Action Button -->
      <button
        class="w-full py-3 px-4 rounded-lg font-medium text-white transition-all duration-300 transform hover:scale-105"
        :style="{ backgroundColor: template.color }"
        @click.stop="$emit('select-template', template)"
      >
        Create Document
      </button>

      <!-- Hover effect overlay -->
      <div class="absolute inset-0 rounded-xl border-2 border-transparent group-hover:border-blue-300 dark:group-hover:border-blue-600 transition-colors duration-300 pointer-events-none"></div>
    </div>

    <!-- Subtle animation effect -->
    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur"></div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  template: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['select-template'])
</script>