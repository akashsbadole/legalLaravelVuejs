import { defineStore } from 'pinia'
import axios from 'axios'

export const useChatbotDocumentStore = defineStore('chatbotDocuments', {
  state: () => ({
    documents: [],
    categories: [],
    stats: {
      total_documents: 0,
      processing: 0,
      storage_used: 0
    },
    currentDocument: null,
    currentCategory: null,
    loading: false,
    error: null,
    searchResults: [],
    totalResults: 0,
    totalPages: 0,
    currentPage: 1,

    // Upload state
    uploadQueue: [],
    uploadProgress: {},

    // Filters
    filters: {
      type: '',
      category: '',
      status: '',
      search: '',
      sort_by: 'created_at',
      sort_direction: 'desc'
    }
  }),

  getters: {
    filteredDocuments: (state) => {
      let filtered = state.documents

      if (state.filters.type) {
        filtered = filtered.filter(doc => doc.document_type === state.filters.type)
      }

      if (state.filters.category) {
        filtered = filtered.filter(doc => doc.category_id === state.filters.category)
      }

      if (state.filters.status) {
        filtered = filtered.filter(doc => doc.processing_status === state.filters.status)
      }

      if (state.filters.search) {
        const search = state.filters.search.toLowerCase()
        filtered = filtered.filter(doc =>
          doc.title.toLowerCase().includes(search) ||
          doc.description?.toLowerCase().includes(search) ||
          doc.original_filename.toLowerCase().includes(search)
        )
      }

      return filtered
    },

    documentsByType: (state) => (type) => {
      return state.documents.filter(doc => doc.document_type === type)
    },

    documentsByCategory: (state) => (categoryId) => {
      return state.documents.filter(doc => doc.category_id === categoryId)
    },

    recentDocuments: (state) => {
      return state.documents
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 5)
    },

    completedUploads: (state) => {
      return state.uploadQueue.filter(item => item.status === 'completed')
    },

    failedUploads: (state) => {
      return state.uploadQueue.filter(item => item.status === 'error')
    },

    activeUploads: (state) => {
      return state.uploadQueue.filter(item =>
        ['pending', 'uploading', 'processing'].includes(item.status)
      )
    }
  },

  actions: {
    async loadDocuments(params = {}) {
      this.loading = true
      this.error = null

      try {
        const queryParams = {
          page: this.currentPage,
          ...this.filters,
          ...params
        }

        const response = await axios.get('/api/chatbot/documents', { params: queryParams })
        this.documents = response.data.data || []
        this.totalResults = response.data.total || 0
        this.totalPages = response.data.last_page || 1
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
        const response = await axios.get(`/api/chatbot/documents/${id}`)
        this.currentDocument = response.data
        return response.data
      } catch (error) {
        this.error = 'Failed to load document'
        throw error
      } finally {
        this.loading = false
      }
    },

    async loadCategories() {
      try {
        const response = await axios.get('/api/chatbot/documents/categories')
        this.categories = response.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load categories'
        throw error
      }
    },

    async loadStats() {
      try {
        const response = await axios.get('/api/chatbot/documents/stats')
        this.stats = response.data || this.stats
        return response.data
      } catch (error) {
        // Don't set error for stats, use default values
        console.warn('Failed to load stats:', error)
      }
    },

    async searchDocuments(params = {}) {
      this.loading = true
      this.error = null

      try {
        const queryParams = {
          ...this.filters,
          ...params
        }

        const response = await axios.get('/api/chatbot/documents/search', { params: queryParams })
        this.searchResults = response.data.results || []
        this.totalResults = response.data.total_results || 0
        this.documents = this.searchResults // Update main documents list
        return response.data
      } catch (error) {
        this.error = 'Search failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createDocument(formData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/chatbot/documents', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            // Update progress if needed
          }
        })

        this.documents.unshift(response.data)
        this.stats.total_documents++
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
        const response = await axios.put(`/api/chatbot/documents/${id}`, data)

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
        await axios.delete(`/api/chatbot/documents/${id}`)

        // Remove from local state
        this.documents = this.documents.filter(doc => doc.id !== id)
        this.stats.total_documents = Math.max(0, this.stats.total_documents - 1)

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

    async downloadDocument(id) {
      try {
        const response = await axios.get(`/api/chatbot/documents/${id}/download`, {
          responseType: 'blob'
        })

        // Create download link
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url

        const contentDisposition = response.headers['content-disposition']
        let filename = 'document'
        if (contentDisposition) {
          const matches = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)
          if (matches && matches[1]) {
            filename = matches[1].replace(/['"]/g, '')
          }
        }

        link.download = filename
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
      } catch (error) {
        this.error = 'Failed to download document'
        throw error
      }
    },

    async createCategory(data) {
      try {
        const response = await axios.post('/api/chatbot/documents/categories', data)
        this.categories.push(response.data)
        return response.data
      } catch (error) {
        this.error = 'Failed to create category'
        throw error
      }
    },

    async updateCategory(id, data) {
      try {
        const response = await axios.put(`/api/chatbot/documents/categories/${id}`, data)

        const index = this.categories.findIndex(cat => cat.id === id)
        if (index >= 0) {
          this.categories[index] = { ...this.categories[index], ...response.data }
        }

        return response.data
      } catch (error) {
        this.error = 'Failed to update category'
        throw error
      }
    },

    async deleteCategory(id) {
      try {
        await axios.delete(`/api/chatbot/documents/categories/${id}`)
        this.categories = this.categories.filter(cat => cat.id !== id)
      } catch (error) {
        this.error = 'Failed to delete category'
        throw error
      }
    },

    // Upload queue management
    addToUploadQueue(files) {
      const queueItems = files.map((file, index) => ({
        id: Date.now() + index,
        file: file,
        name: file.name,
        size: file.size,
        type: file.type,
        status: 'pending',
        progress: 0,
        error: null,
        processingSteps: [
          { name: 'Validating file', status: 'pending' },
          { name: 'Uploading to server', status: 'pending' },
          { name: 'Processing content', status: 'pending' },
          { name: 'Indexing for search', status: 'pending' }
        ]
      }))

      this.uploadQueue.push(...queueItems)
    },

    updateUploadProgress(id, progress, status = null, error = null) {
      const item = this.uploadQueue.find(item => item.id === id)
      if (item) {
        if (progress !== undefined) item.progress = progress
        if (status) item.status = status
        if (error) item.error = error
      }
    },

    removeFromUploadQueue(id) {
      this.uploadQueue = this.uploadQueue.filter(item => item.id !== id)
    },

    clearUploadQueue() {
      this.uploadQueue = []
    },

    // Filter management
    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {
        type: '',
        category: '',
        status: '',
        search: '',
        sort_by: 'created_at',
        sort_direction: 'desc'
      }
    },

    // Pagination
    setPage(page) {
      this.currentPage = page
    },

    // Error handling
    clearError() {
      this.error = null
    },

    // Current document management
    setCurrentDocument(document) {
      this.currentDocument = document
    },

    clearCurrentDocument() {
      this.currentDocument = null
    }
  }
})