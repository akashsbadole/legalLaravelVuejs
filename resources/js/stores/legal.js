import { defineStore } from 'pinia'
import axios from 'axios'

export const useLegalStore = defineStore('legal', {
  state: () => ({
    searchResults: [],
    totalResults: 0,
    loading: false,
    error: null,

    // Cached data
    ipcSections: [],
    crpcSections: [],
    cpcSections: [],
    constitutionArticles: [],
    stateLaws: [],
    legalJudgments: [],

    // Current search parameters
    lastSearchQuery: '',
    lastSearchType: 'all'
  }),

  getters: {
    getSectionByType: (state) => (type) => {
      switch (type) {
        case 'ipc': return state.ipcSections
        case 'crpc': return state.crpcSections
        case 'cpc': return state.cpcSections
        case 'constitution': return state.constitutionArticles
        case 'state_law': return state.stateLaws
        case 'judgment': return state.legalJudgments
        default: return []
      }
    }
  },

  actions: {
    async searchLegalDatabase({ q, type = 'all', limit = 20 }) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/legal/search', {
          params: { q, type, limit }
        })

        this.searchResults = response.data.results || []
        this.totalResults = response.data.total_results || 0
        this.lastSearchQuery = q
        this.lastSearchType = type

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Search failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async advancedSearch(filters) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/legal/search/advanced', {
          filters
        })

        this.searchResults = []
        // Combine results from different categories
        Object.values(response.data.results || {}).forEach(categoryResults => {
          if (Array.isArray(categoryResults)) {
            this.searchResults.push(...categoryResults)
          }
        })

        this.totalResults = this.searchResults.length

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Advanced search failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async loadIpcSections(params = {}) {
      try {
        const response = await axios.get('/api/legal/ipc', { params })
        this.ipcSections = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load IPC sections'
        throw error
      }
    },

    async loadCrpcSections(params = {}) {
      try {
        const response = await axios.get('/api/legal/crpc', { params })
        this.crpcSections = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load CrPC sections'
        throw error
      }
    },

    async loadCpcSections(params = {}) {
      try {
        const response = await axios.get('/api/legal/cpc', { params })
        this.cpcSections = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load CPC sections'
        throw error
      }
    },

    async loadConstitutionArticles(params = {}) {
      try {
        const response = await axios.get('/api/legal/constitution', { params })
        this.constitutionArticles = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load Constitution articles'
        throw error
      }
    },

    async loadStateLaws(params = {}) {
      try {
        const response = await axios.get('/api/legal/state-laws', { params })
        this.stateLaws = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load state laws'
        throw error
      }
    },

    async loadLegalJudgments(params = {}) {
      try {
        const response = await axios.get('/api/legal/judgments', { params })
        this.legalJudgments = response.data.data || []
        return response.data
      } catch (error) {
        this.error = 'Failed to load legal judgments'
        throw error
      }
    },

    async getSectionDetails(type, id) {
      try {
        const response = await axios.get(`/api/legal/${type}/${id}`)
        return response.data
      } catch (error) {
        this.error = `Failed to load ${type} details`
        throw error
      }
    },

    sortResults(sortBy) {
      switch (sortBy) {
        case 'title':
          this.searchResults.sort((a, b) => a.title.localeCompare(b.title))
          break
        case 'type':
          this.searchResults.sort((a, b) => a.type.localeCompare(b.type))
          break
        case 'relevance':
        default:
          // Keep original order (assuming API returns relevance-ordered results)
          break
      }
    },

    clearSearch() {
      this.searchResults = []
      this.totalResults = 0
      this.lastSearchQuery = ''
      this.lastSearchType = 'all'
      this.error = null
    },

    clearError() {
      this.error = null
    }
  }
})