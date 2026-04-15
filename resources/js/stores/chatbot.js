import { defineStore } from 'pinia'
import axios from 'axios'

export const useChatbotStore = defineStore('chatbot', {
  state: () => ({
    conversations: [],
    currentConversation: null,
    isTyping: false,
    error: null,
    rateLimitRemaining: 50,
    rateLimitReset: null
  }),

  getters: {
    getCurrentConversationMessages: (state) => {
      return state.currentConversation?.messages || []
    },

    isRateLimited: (state) => {
      return state.rateLimitRemaining <= 0
    }
  },

  actions: {
    async sendMessage(message, conversationId = null) {
      if (this.isRateLimited) {
        throw new Error('Rate limit exceeded. Please try again later.')
      }

      this.isTyping = true
      this.error = null

      try {
        const payload = {
          message: message,
          conversation_id: conversationId || this.currentConversation?.id
        }

        const response = await axios.post('/api/legal/chatbot/message', payload)

        // Update rate limit info
        if (response.headers['x-ratelimit-remaining']) {
          this.rateLimitRemaining = parseInt(response.headers['x-ratelimit-remaining'])
        }
        if (response.headers['x-ratelimit-reset']) {
          this.rateLimitReset = new Date(parseInt(response.headers['x-ratelimit-reset']) * 1000)
        }

        // Update conversation
        if (response.data.conversation) {
          this.updateConversation(response.data.conversation)
        }

        return {
          message: response.data.message,
          references: response.data.references || [],
          conversation: response.data.conversation
        }

      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to send message'
        throw error
      } finally {
        this.isTyping = false
      }
    },

    async startNewConversation(title = null) {
      try {
        const response = await axios.post('/api/legal/chatbot/conversation', {
          title: title || 'New Legal Consultation'
        })

        this.currentConversation = response.data.conversation
        return response.data.conversation
      } catch (error) {
        this.error = 'Failed to start conversation'
        throw error
      }
    },

    async loadConversations() {
      try {
        const response = await axios.get('/api/legal/chatbot/conversations')
        this.conversations = response.data.conversations || []
        return this.conversations
      } catch (error) {
        this.error = 'Failed to load conversations'
        throw error
      }
    },

    async loadConversation(conversationId) {
      try {
        const response = await axios.get(`/api/legal/chatbot/conversation/${conversationId}`)
        this.currentConversation = response.data.conversation
        return this.currentConversation
      } catch (error) {
        this.error = 'Failed to load conversation'
        throw error
      }
    },

    async deleteConversation(conversationId) {
      try {
        await axios.delete(`/api/legal/chatbot/conversation/${conversationId}`)
        this.conversations = this.conversations.filter(conv => conv.id !== conversationId)
        if (this.currentConversation?.id === conversationId) {
          this.currentConversation = null
        }
      } catch (error) {
        this.error = 'Failed to delete conversation'
        throw error
      }
    },

    updateConversation(conversationData) {
      const index = this.conversations.findIndex(conv => conv.id === conversationData.id)
      if (index >= 0) {
        this.conversations[index] = { ...this.conversations[index], ...conversationData }
      } else {
        this.conversations.unshift(conversationData)
      }

      // Set as current conversation if none is set or if it matches
      if (!this.currentConversation || this.currentConversation.id === conversationData.id) {
        this.currentConversation = conversationData
      }
    },

    setCurrentConversation(conversation) {
      this.currentConversation = conversation
    },

    clearCurrentConversation() {
      this.currentConversation = null
    },

    clearError() {
      this.error = null
    }
  }
})