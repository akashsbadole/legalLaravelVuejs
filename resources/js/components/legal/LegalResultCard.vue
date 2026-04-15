<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <!-- Title and Type Badge -->
        <div class="flex items-center gap-3 mb-2">
          <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 cursor-pointer" @click="$emit('view-details', result)">
            {{ result.title }}
          </h3>
          <span :class="getTypeBadgeClass(result.type)" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ getTypeLabel(result.type) }}
          </span>
        </div>

        <!-- Section/Article Number -->
        <div class="text-sm text-gray-600 mb-2">
          <span v-if="result.section_number">Section {{ result.section_number }}</span>
          <span v-else-if="result.article_number">Article {{ result.article_number }}</span>
          <span v-if="result.subsection" class="ml-1">({{ result.subsection }})</span>
        </div>

        <!-- Description -->
        <p class="text-gray-700 mb-3">{{ result.description }}</p>

        <!-- Metadata -->
        <div class="flex flex-wrap gap-4 text-sm text-gray-500">
          <span v-if="result.classification">
            <strong>Classification:</strong> {{ result.classification }}
          </span>
          <span v-if="result.cognizable">
            <strong>Cognizable:</strong> {{ result.cognizable }}
          </span>
          <span v-if="result.court_level">
            <strong>Court:</strong> {{ result.court_level }}
          </span>
          <span v-if="result.category">
            <strong>Category:</strong> {{ result.category }}
          </span>
          <span v-if="result.part">
            <strong>Part:</strong> {{ result.part }}
          </span>
        </div>
      </div>

      <!-- Action Button -->
      <div class="ml-4">
        <button
          @click="$emit('view-details', result)"
          class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
        >
          View Details
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  result: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['view-details'])

const getTypeBadgeClass = (type) => {
  const classes = {
    ipc: 'bg-red-100 text-red-800',
    crpc: 'bg-orange-100 text-orange-800',
    cpc: 'bg-blue-100 text-blue-800',
    constitution: 'bg-green-100 text-green-800',
    judgment: 'bg-purple-100 text-purple-800',
    state_law: 'bg-yellow-100 text-yellow-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getTypeLabel = (type) => {
  const labels = {
    ipc: 'IPC',
    crpc: 'CrPC',
    cpc: 'CPC',
    constitution: 'Constitution',
    judgment: 'Judgment',
    state_law: 'State Law'
  }
  return labels[type] || type.toUpperCase()
}
</script>