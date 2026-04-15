import { defineStore } from 'pinia'
import axios from 'axios'

export const useDocumentStore = defineStore('documents', {
  state: () => ({
    documents: [],
    templates: [],
    currentDocument: null,
    currentTemplate: null,
    loading: false,
    error: null,
    searchResults: [],
    totalResults: 0,

    // AI features
    aiAnalysis: null,
    aiSuggestions: [],
    complianceReport: null,
    translationResult: null,

    // Filters and pagination
    filters: {
      type: '',
      status: '',
      dateRange: '',
      search: ''
    },
    pagination: {
      page: 1,
      perPage: 20,
      total: 0
    }
  }),

  getters: {
    filteredDocuments: (state) => {
      let filtered = state.documents

      if (state.filters.type) {
        filtered = filtered.filter(doc => doc.type === state.filters.type)
      }

      if (state.filters.status) {
        filtered = filtered.filter(doc => doc.status === state.filters.status)
      }

      if (state.filters.search) {
        const search = state.filters.search.toLowerCase()
        filtered = filtered.filter(doc =>
          doc.title.toLowerCase().includes(search) ||
          doc.description?.toLowerCase().includes(search)
        )
      }

      return filtered
    },

    documentsByStatus: (state) => (status) => {
      return state.documents.filter(doc => doc.status === status)
    },

    documentsByType: (state) => (type) => {
      return state.documents.filter(doc => doc.type === type)
    },

    recentDocuments: (state) => {
      return state.documents
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 5)
    },

    documentStats: (state) => {
      const stats = {
        total: state.documents.length,
        draft: 0,
        review: 0,
        approved: 0,
        completed: 0,
        archived: 0
      }

      state.documents.forEach(doc => {
        if (stats[doc.status] !== undefined) {
          stats[doc.status]++
        }
      })

      return stats
    }
  },

  actions: {
    async loadDocuments(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/documents', { params })
        this.documents = response.data.data || []
        this.pagination.total = response.data.total || 0
        return response.data
      } catch (error) {
        this.error = 'Failed to load documents'
        throw error
      } finally {
        this.loading = false
      }
    },

    async loadDocument(id) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get(`/api/documents/${id}`)
        this.currentDocument = response.data
        return response.data
      } catch (error) {
        this.error = 'Failed to load document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createDocument(templateId, data) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/documents', {
          template_id: templateId,
          ...data
        })
        this.documents.unshift(response.data)
        return response.data
      } catch (error) {
        this.error = 'Failed to create document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateDocument(id, data) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.put(`/api/documents/${id}`, data)

        // Update in local state
        const index = this.documents.findIndex(doc => doc.id === id)
        if (index >= 0) {
          this.documents[index] = { ...this.documents[index], ...response.data }
        }

        if (this.currentDocument?.id === id) {
          this.currentDocument = { ...this.currentDocument, ...response.data }
        }

        return response.data
      } catch (error) {
        this.error = 'Failed to update document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteDocument(id) {
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/api/documents/${id}`)

        // Remove from local state
        this.documents = this.documents.filter(doc => doc.id !== id)

        if (this.currentDocument?.id === id) {
          this.currentDocument = null
        }
      } catch (error) {
        this.error = 'Failed to delete document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async loadTemplates() {
      try {
        const response = await axios.get('/api/document-templates')
        this.templates = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load templates'
        throw error
      }
    },

    async analyzeDocument(id, content) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post(`/api/documents/${id}/analyze`, {
          content: content
        })
        this.aiAnalysis = response.data
        return response.data
      } catch (error) {
        this.error = 'Failed to analyze document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async checkCompliance(id, content, documentType) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post(`/api/documents/${id}/compliance`, {
          content: content,
          document_type: documentType
        })
        this.complianceReport = response.data
        return response.data
      } catch (error) {
        this.error = 'Failed to check compliance'
        throw error
      } finally {
        this.loading = false
      }
    },

    async generateContent(templateId, userData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/ai/generate-content', {
          template_id: templateId,
          user_data: userData
        })
        return response.data
      } catch (error) {
        this.error = 'Failed to generate content'
        throw error
      } finally {
        this.loading = false
      }
    },

    async translateDocument(id, content, targetLanguage) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post(`/api/documents/${id}/translate`, {
          content: content,
          target_language: targetLanguage
        })
        this.translationResult = response.data
        return response.data
      } catch (error) {
        this.error = 'Failed to translate document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async searchDocuments(query, filters = {}) {
      this.loading = true
      this.error = null

      try {
        const params = {
          q: query,
          ...filters
        }
        const response = await axios.get('/api/documents/search', { params })
        this.searchResults = response.data.results || []
        this.totalResults = response.data.total || 0
        return response.data
      } catch (error) {
        this.error = 'Search failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    setCurrentDocument(document) {
      this.currentDocument = document
    },

    setCurrentTemplate(template) {
      this.currentTemplate = template
    },

    updateFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {
        type: '',
        status: '',
        dateRange: '',
        search: ''
      }
    },

    clearSearch() {
      this.searchResults = []
      this.totalResults = 0
    },

    clearError() {
      this.error = null
    },

    clearAIResults() {
      this.aiAnalysis = null
      this.aiSuggestions = []
      this.complianceReport = null
      this.translationResult = null
    }
  }
})