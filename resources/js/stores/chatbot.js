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

      if (this.currentConversation?.id === conversationData.id) {
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
    },

    // Mock method for development (replace with actual API call)
    async mockSendMessage(message) {
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000))

      // Mock response based on message content
      let response = ''
      let references = []

      if (message.toLowerCase().includes('murder') || message.toLowerCase().includes('302')) {
        response = `Section 302 of the Indian Penal Code deals with punishment for murder. According to this section, whoever commits murder shall be punished with death, or imprisonment for life, and shall also be liable to fine.

This is a non-bailable, cognizable offense investigated by police. The offense is compoundable only in cases where the victim survives and compounding is permitted under Section 320 CrPC.`
        references = [
          {
            id: 1,
            type: 'ipc',
            section_number: 302,
            title: 'Punishment for murder',
            description: 'Whoever commits murder shall be punished with death, or imprisonment for life...'
          }
        ]
      } else if (message.toLowerCase().includes('theft') || message.toLowerCase().includes('378')) {
        response = `Section 378 IPC defines theft as "Whoever, intending to take dishonestly any movable property out of the possession of any person without that person's consent, moves that property in order to such taking, is said to commit theft."

The punishment under Section 379 IPC is imprisonment up to 3 years, or fine, or both. This is a bailable offense.`
        references = [
          {
            id: 2,
            type: 'ipc',
            section_number: 378,
            title: 'Theft',
            description: 'Definition of theft under IPC...'
          }
        ]
      } else if (message.toLowerCase().includes('fundamental rights') || message.toLowerCase().includes('article 14')) {
        response = `Article 14 of the Indian Constitution guarantees equality before law and equal protection of laws. This is one of the fundamental rights enshrined in Part III of the Constitution.

The Supreme Court has interpreted this article extensively, establishing the doctrine of reasonable classification and prohibiting arbitrary state action.`
        references = [
          {
            id: 3,
            type: 'constitution',
            article_number: '14',
            title: 'Equality before law',
            description: 'The State shall not deny to any person equality before the law...'
          }
        ]
      } else {
        response = `I understand you're asking about "${message}". While I have access to Indian legal statutes including IPC, CrPC, CPC, and the Constitution, I need more specific details to provide accurate information.

Could you please clarify:
- Are you looking for a specific section or article?
- Is this related to criminal law, civil procedure, or constitutional law?
- What specific aspect would you like to know about?

Remember, this is for informational purposes only and not legal advice.`
      }

      return {
        message: response,
        references: references
      }
    }
  }
})