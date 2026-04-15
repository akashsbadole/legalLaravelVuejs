<template>
  <div :class="messageClasses">
    <div class="flex items-start gap-3">
      <!-- Avatar -->
      <div :class="avatarClasses">
        <svg v-if="message.type === 'user'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <svg v-else-if="message.type === 'ai'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
      </div>

      <!-- Message Content -->
      <div class="flex-1">
        <!-- Message Header -->
        <div class="flex items-center gap-2 mb-1">
          <span :class="senderNameClasses">
            {{ message.type === 'user' ? 'You' : 'Legal AI' }}
          </span>
          <span class="text-xs text-gray-500">
            {{ formatTime(message.timestamp) }}
          </span>
        </div>

        <!-- Message Text -->
        <div v-if="!isTyping" class="prose prose-sm max-w-none" v-html="formattedContent"></div>

        <!-- Typing Indicator -->
        <div v-else class="flex items-center gap-1">
          <div class="flex gap-1">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          </div>
          <span class="text-sm text-gray-500 ml-2">Legal AI is thinking...</span>
        </div>

        <!-- References -->
        <div v-if="message.references && message.references.length > 0" class="mt-3">
          <h4 class="text-sm font-medium text-gray-900 mb-2">Legal References:</h4>
          <div class="space-y-2">
            <div
              v-for="reference in message.references"
              :key="reference.id"
              class="bg-gray-50 rounded-lg p-3 border border-gray-200"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span :class="getReferenceTypeClass(reference.type)" class="px-2 py-1 text-xs font-medium rounded">
                      {{ getReferenceTypeLabel(reference.type) }}
                    </span>
                    <span v-if="reference.section_number" class="text-sm font-medium text-gray-900">
                      Section {{ reference.section_number }}
                    </span>
                    <span v-else-if="reference.article_number" class="text-sm font-medium text-gray-900">
                      Article {{ reference.article_number }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-700">{{ reference.title }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ reference.description?.substring(0, 100) }}...</p>
                </div>
                <button
                  @click="viewReference(reference)"
                  class="ml-3 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  View
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: {
    type: Object,
    required: true
  },
  isTyping: {
    type: Boolean,
    default: false
  }
})

const messageClasses = computed(() => {
  const baseClasses = 'rounded-lg p-4'
  if (props.message.type === 'user') {
    return baseClasses + ' bg-blue-50 border border-blue-200 ml-12'
  } else if (props.message.type === 'ai') {
    return baseClasses + ' bg-gray-50 border border-gray-200 mr-12'
  } else {
    return baseClasses + ' bg-red-50 border border-red-200'
  }
})

const avatarClasses = computed(() => {
  const baseClasses = 'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0'
  if (props.message.type === 'user') {
    return baseClasses + ' bg-blue-600 text-white'
  } else if (props.message.type === 'ai') {
    return baseClasses + ' bg-green-600 text-white'
  } else {
    return baseClasses + ' bg-red-600 text-white'
  }
})

const senderNameClasses = computed(() => {
  if (props.message.type === 'user') {
    return 'text-sm font-medium text-blue-900'
  } else if (props.message.type === 'ai') {
    return 'text-sm font-medium text-green-900'
  } else {
    return 'text-sm font-medium text-red-900'
  }
})

const formattedContent = computed(() => {
  if (!props.message.content) return ''

  // Simple markdown-like formatting
  return props.message.content
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/\n/g, '<br>')
})

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const getReferenceTypeClass = (type) => {
  const classes = {
    ipc: 'bg-red-100 text-red-800',
    crpc: 'bg-orange-100 text-orange-800',
    cpc: 'bg-blue-100 text-blue-800',
    constitution: 'bg-green-100 text-green-800',
    judgment: 'bg-purple-100 text-purple-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getReferenceTypeLabel = (type) => {
  const labels = {
    ipc: 'IPC',
    crpc: 'CrPC',
    cpc: 'CPC',
    constitution: 'Constitution',
    judgment: 'Judgment'
  }
  return labels[type] || type.toUpperCase()
}

const viewReference = (reference) => {
  // Navigate to reference details
  console.log('View reference:', reference)
}
</script>