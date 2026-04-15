<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <!-- Section Number and Title -->
        <div class="flex items-center gap-3 mb-3">
          <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <span class="text-lg font-bold text-red-600">{{ section.section_number }}</span>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ section.title }}</h3>
            <div class="flex gap-2 mt-1">
              <span :class="getClassificationBadgeClass(section.classification)" class="px-2 py-1 text-xs font-medium rounded-full">
                {{ section.classification }}
              </span>
              <span :class="getCognizableBadgeClass(section.cognizable)" class="px-2 py-1 text-xs font-medium rounded-full">
                {{ section.cognizable }}
              </span>
            </div>
          </div>
        </div>

        <!-- Description -->
        <p class="text-gray-700 mb-4 line-clamp-3">{{ section.description }}</p>

        <!-- Penalty Information -->
        <div v-if="section.penalty" class="mb-4">
          <h4 class="text-sm font-medium text-gray-900 mb-1">Penalty:</h4>
          <p class="text-sm text-gray-600">{{ section.penalty }}</p>
        </div>

        <!-- Punishment Details -->
        <div v-if="section.punishment_details" class="mb-4">
          <h4 class="text-sm font-medium text-gray-900 mb-1">Punishment:</h4>
          <p class="text-sm text-gray-600">{{ section.punishment_details }}</p>
        </div>

        <!-- Related Sections -->
        <div v-if="section.related_sections && section.related_sections.length > 0" class="mb-4">
          <h4 class="text-sm font-medium text-gray-900 mb-1">Related Sections:</h4>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="relatedSection in section.related_sections"
              :key="relatedSection"
              class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"
            >
              Section {{ relatedSection }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="ml-4 flex flex-col gap-2">
        <button
          @click="$emit('view-details', section)"
          class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
        >
          View Details
        </button>
        <button
          @click="searchRelated"
          class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors"
        >
          Related Cases
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  section: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['view-details'])

const getClassificationBadgeClass = (classification) => {
  const classes = {
    'bailable': 'bg-green-100 text-green-800',
    'non-bailable': 'bg-red-100 text-red-800',
    'compoundable': 'bg-blue-100 text-blue-800',
    'non-compoundable': 'bg-orange-100 text-orange-800'
  }
  return classes[classification] || 'bg-gray-100 text-gray-800'
}

const getCognizableBadgeClass = (cognizable) => {
  const classes = {
    'cognizable': 'bg-purple-100 text-purple-800',
    'non-cognizable': 'bg-yellow-100 text-yellow-800'
  }
  return classes[cognizable] || 'bg-gray-100 text-gray-800'
}

const searchRelated = () => {
  // Implement search for related cases or sections
  console.log('Search related for section:', props.section.section_number)
}
</script>