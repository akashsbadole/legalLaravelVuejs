<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Legal AI Assistant</h1>
        <p class="text-gray-600">Ask questions about Indian law and get AI-powered responses with legal references</p>

        <!-- Disclaimer -->
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-yellow-800">Important Disclaimer</h3>
              <div class="mt-2 text-sm text-yellow-700">
                <p>AI responses are for informational purposes only and do not constitute legal advice. Always consult qualified legal professionals for specific legal matters. The AI may not have access to the most recent legal developments.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Interface -->
      <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white px-6 py-4">
          <h2 class="text-lg font-semibold">Legal Consultation</h2>
          <p class="text-blue-100 text-sm">Powered by AI with Indian legal database</p>
        </div>

        <!-- Chat Messages -->
        <div ref="chatContainer" class="h-96 overflow-y-auto px-6 py-4 space-y-4">
          <!-- Welcome Message -->
          <div v-if="messages.length === 0" class="text-center py-8">
            <div class="text-gray-400 mb-4">
              <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Welcome to Legal AI Assistant</h3>
            <p class="text-gray-600 mb-4">Ask me anything about Indian law, IPC, CrPC, CPC, or Constitution</p>
            <div class="flex flex-wrap gap-2 justify-center">
              <button
                v-for="suggestion in quickQuestions"
                :key="suggestion"
                @click="askQuestion(suggestion)"
                class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition-colors text-sm"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>

          <!-- Messages -->
          <ChatMessage
            v-for="(message, index) in messages"
            :key="index"
            :message="message"
            :is-typing="isTyping && index === messages.length - 1"
          />
        </div>

        <!-- Chat Input -->
        <div class="border-t border-gray-200 px-6 py-4">
          <div class="flex gap-3">
            <div class="flex-1">
              <textarea
                v-model="currentMessage"
                @keydown.enter.exact.prevent="sendMessage"
                placeholder="Ask your legal question..."
                rows="1"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                :disabled="isTyping"
              ></textarea>
            </div>
            <button
              @click="sendMessage"
              :disabled="!currentMessage.trim() || isTyping"
              class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="isTyping" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-else>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </span>
            </button>
          </div>

          <!-- Rate Limiting Info -->
          <div class="mt-2 text-xs text-gray-500">
            <span>Questions remaining today: {{ remainingQuestions }}</span>
          </div>
        </div>
      </div>

      <!-- Recent Conversations -->
      <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Conversations</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="conversation in recentConversations"
            :key="conversation.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer"
            @click="loadConversation(conversation)"
          >
            <h4 class="font-medium text-gray-900 mb-1">{{ conversation.title }}</h4>
            <p class="text-sm text-gray-600 mb-2">{{ conversation.preview }}</p>
            <div class="flex justify-between items-center text-xs text-gray-500">
              <span>{{ formatDate(conversation.created_at) }}</span>
              <span>{{ conversation.message_count }} messages</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useChatbotStore } from '@/stores/chatbot'
import AppLayout from '@/layouts/AppLayout.vue'
import ChatMessage from '@/components/legal/ChatMessage.vue'

const chatbotStore = useChatbotStore()

const messages = ref([])
const currentMessage = ref('')
const isTyping = ref(false)
const remainingQuestions = ref(50) // Mock rate limiting
const chatContainer = ref(null)

const quickQuestions = [
  'What is the punishment for theft under IPC?',
  'Explain Section 302 of IPC',
  'What are fundamental rights under the Constitution?',
  'How long can police detain someone without charges?',
  'What is the difference between bailable and non-bailable offenses?'
]

const recentConversations = ref([])

const loadRecentConversations = async () => {
  try {
    const response = await axios.get('/api/legal/chatbot/conversations')
    recentConversations.value = response.data.conversations?.slice(0, 6) || []
  } catch (error) {
    console.error('Failed to load conversations:', error)
  }
}

const sendMessage = async () => {
  if (!currentMessage.value.trim() || isTyping.value) return

  const userMessage = {
    id: Date.now(),
    type: 'user',
    content: currentMessage.value.trim(),
    timestamp: new Date()
  }

  messages.value.push(userMessage)
  const messageText = currentMessage.value
  currentMessage.value = ''
  isTyping.value = true

  // Scroll to bottom
  await nextTick()
  scrollToBottom()

  try {
    const response = await chatbotStore.sendMessage(messageText)

    const aiMessage = {
      id: Date.now() + 1,
      type: 'ai',
      content: response.message,
      references: response.references || [],
      timestamp: new Date()
    }

    messages.value.push(aiMessage)
    remainingQuestions.value = Math.max(0, remainingQuestions.value - 1)
  } catch (error) {
    const errorMessage = {
      id: Date.now() + 1,
      type: 'error',
      content: 'Sorry, I encountered an error. Please try again.',
      timestamp: new Date()
    }
    messages.value.push(errorMessage)
  } finally {
    isTyping.value = false
    await nextTick()
    scrollToBottom()
  }
}

const askQuestion = (question) => {
  currentMessage.value = question
  sendMessage()
}

const loadConversation = (conversation) => {
  // Load conversation history
  console.log('Load conversation:', conversation)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

onMounted(() => {
  scrollToBottom()
  loadRecentConversations()
})
</script>