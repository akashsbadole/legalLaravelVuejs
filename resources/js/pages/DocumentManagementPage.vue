<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">AI Document Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Create, analyze, and manage legal documents with AI assistance</p>
          </div>

          <!-- Theme Toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-white/20 rounded-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </div>
            <span class="text-2xl font-bold">New</span>
          </div>
          <h3 class="text-lg font-semibold mb-2">Create Document</h3>
          <p class="text-blue-100 text-sm mb-4">Start with AI-powered templates</p>
          <button class="w-full bg-white text-blue-600 py-2 px-4 rounded-lg font-medium hover:bg-blue-50 transition-colors">
            Create New
          </button>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-white/20 rounded-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <span class="text-2xl font-bold">AI</span>
          </div>
          <h3 class="text-lg font-semibold mb-2">Analyze Document</h3>
          <p class="text-green-100 text-sm mb-4">Get AI insights and compliance checks</p>
          <button class="w-full bg-white text-green-600 py-2 px-4 rounded-lg font-medium hover:bg-green-50 transition-colors">
            Analyze Now
          </button>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-white/20 rounded-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-2xl font-bold">Scan</span>
          </div>
          <h3 class="text-lg font-semibold mb-2">Upload & Scan</h3>
          <p class="text-purple-100 text-sm mb-4">Extract text from PDFs and images</p>
          <button class="w-full bg-white text-purple-600 py-2 px-4 rounded-lg font-medium hover:bg-purple-50 transition-colors">
            Upload File
          </button>
        </div>
      </div>

      <!-- Document Templates Grid -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Legal Document Templates</h2>
          <div class="flex gap-2">
            <input
              v-model="templateSearch"
              type="text"
              placeholder="Search templates..."
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <select
              v-model="selectedCategory"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Categories</option>
              <option value="nda">NDA</option>
              <option value="employment_contract">Employment Contract</option>
              <option value="service_agreement">Service Agreement</option>
              <option value="lease_agreement">Lease Agreement</option>
              <option value="power_of_attorney">Power of Attorney</option>
              <option value="legal_notice">Legal Notice</option>
              <option value="court_petition">Court Petition</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <DocumentTemplateCard
            v-for="template in filteredTemplates"
            :key="template.slug"
            :template="template"
            @select-template="selectTemplate"
          />
        </div>
      </div>

      <!-- Recent Documents -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recent Documents</h2>
          <router-link to="/documents" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            View All →
          </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <RecentDocumentCard
            v-for="document in recentDocuments"
            :key="document.id"
            :document="document"
          />
        </div>
      </div>

      <!-- AI Usage Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">AI Requests Today</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ aiStats.requests_today }}</p>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Documents Created</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ aiStats.documents_created }}</p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Compliance Score</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ aiStats.avg_compliance }}%</p>
            </div>
            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useDocumentStore } from '@/stores/documents'
import AppLayout from '@/layouts/AppLayout.vue'
import DocumentTemplateCard from '@/components/documents/DocumentTemplateCard.vue'
import RecentDocumentCard from '@/components/documents/RecentDocumentCard.vue'

const documentStore = useDocumentStore()

const isDarkMode = ref(false)
const templateSearch = ref('')
const selectedCategory = ref('')
const aiStats = ref({
  requests_today: 0,
  documents_created: 0,
  avg_compliance: 0
})

// Mock data for templates
const templates = ref([
  {
    slug: 'nda',
    name: 'Non-Disclosure Agreement',
    category: 'nda',
    description: 'Protect confidential information and trade secrets',
    icon: '🔒',
    color: '#EF4444',
    placeholders: ['party_a_name', 'party_b_name', 'confidential_info', 'term']
  },
  {
    slug: 'employment_contract',
    name: 'Employment Contract',
    category: 'employment_contract',
    description: 'Comprehensive employment agreement with terms and conditions',
    icon: '👔',
    color: '#3B82F6',
    placeholders: ['employee_name', 'employer_name', 'position', 'salary', 'start_date']
  },
  {
    slug: 'service_agreement',
    name: 'Service Agreement',
    category: 'service_agreement',
    description: 'Agreement for service provision between parties',
    icon: '🤝',
    color: '#10B981',
    placeholders: ['service_provider', 'client', 'services', 'payment_terms']
  },
  {
    slug: 'lease_agreement',
    name: 'Lease Agreement',
    category: 'lease_agreement',
    description: 'Property lease agreement for residential or commercial use',
    icon: '🏠',
    color: '#F59E0B',
    placeholders: ['landlord_name', 'tenant_name', 'property_address', 'rent_amount']
  },
  {
    slug: 'power_of_attorney',
    name: 'Power of Attorney',
    category: 'power_of_attorney',
    description: 'Grant authority to another person to act on your behalf',
    icon: '📝',
    color: '#8B5CF6',
    placeholders: ['principal_name', 'attorney_name', 'powers_granted', 'duration']
  },
  {
    slug: 'legal_notice',
    name: 'Legal Notice',
    category: 'legal_notice',
    description: 'Formal notice for legal disputes and demands',
    icon: '⚖️',
    color: '#DC2626',
    placeholders: ['sender_details', 'receiver_details', 'subject', 'demands']
  },
  {
    slug: 'court_petition',
    name: 'Court Petition',
    category: 'court_petition',
    description: 'Formal petition filed in court for legal proceedings',
    icon: '🏛️',
    color: '#1F2937',
    placeholders: ['court_name', 'petitioner_name', 'respondent_name', 'prayer']
  },
  {
    slug: 'lease_deed',
    name: 'Lease Deed',
    category: 'lease_deed',
    description: 'Formal lease deed for property rental agreements',
    icon: '📄',
    color: '#059669',
    placeholders: ['lessor_name', 'lessee_name', 'property_details', 'lease_period']
  }
])

const recentDocuments = ref([
  {
    id: 1,
    title: 'Employment Contract - John Doe',
    type: 'employment_contract',
    status: 'completed',
    created_at: '2024-01-15',
    completion_percentage: 100
  },
  {
    id: 2,
    title: 'NDA - Tech Startup',
    type: 'nda',
    status: 'review',
    created_at: '2024-01-14',
    completion_percentage: 85
  },
  {
    id: 3,
    title: 'Service Agreement - Consulting',
    type: 'service_agreement',
    status: 'draft',
    created_at: '2024-01-13',
    completion_percentage: 60
  }
])

const filteredTemplates = computed(() => {
  return templates.value.filter(template => {
    const matchesSearch = !templateSearch.value ||
      template.name.toLowerCase().includes(templateSearch.value.toLowerCase()) ||
      template.description.toLowerCase().includes(templateSearch.value.toLowerCase())

    const matchesCategory = !selectedCategory.value || template.category === selectedCategory.value

    return matchesSearch && matchesCategory
  })
})

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  // In a real app, you'd persist this preference
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}

const selectTemplate = (template) => {
  // Navigate to template creation page
  console.log('Selected template:', template)
}

onMounted(() => {
  // Load AI stats and recent documents
  loadStats()
})

const loadStats = () => {
  // In a real app, fetch from API
  aiStats.value = {
    requests_today: 24,
    documents_created: 8,
    avg_compliance: 87
  }
}
</script>