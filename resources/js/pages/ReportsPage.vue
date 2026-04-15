<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive insights into your legal practice performance</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <div class="text-red-600 dark:text-red-400 mr-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-red-800 dark:text-red-200 font-medium">Error loading reports</p>
                        <p class="text-red-700 dark:text-red-300 text-sm">{{ error }}</p>
                        <button @click="loadReportData" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-500 underline">
                            Try again
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading && !error" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>

            <div v-else-if="!error" class="space-y-6">
                <!-- Report Type Navigation -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Report Types</h3>
                            <div class="flex space-x-2">
                                <button @click="changeReportType('overview')"
                                    :class="reportType === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white hover:bg-gray-700'"
                                    class="px-4 py-2 rounded-lg transition">
                                    Overview
                                </button>
                                <button @click="changeReportType('financial')"
                                    :class="reportType === 'financial' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white hover:bg-gray-700'"
                                    class="px-4 py-2 rounded-lg transition">
                                    Financial
                                </button>
                                <button @click="changeReportType('cases')"
                                    :class="reportType === 'cases' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white hover:bg-gray-700'"
                                    class="px-4 py-2 rounded-lg transition">
                                    Cases
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overview Report -->
                <div v-if="reportType === 'overview'" class="space-y-6">
                    <!-- Key Metrics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                                    <p class="text-2xl font-bold mt-1">{{ formatMoney(stats.totalRevenue) }}</p>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">Outstanding</p>
                                    <p class="text-2xl font-bold mt-1">{{ formatMoney(stats.outstandingAmount) }}</p>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-100 text-sm font-medium">Overdue</p>
                                    <p class="text-2xl font-bold mt-1">{{ formatMoney(stats.overdueAmount) }}</p>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm font-medium">Active Cases</p>
                                    <p class="text-2xl font-bold mt-1">{{ stats.activeCases }}</p>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Analytics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
                            <div class="space-y-4">
                                <div v-for="month in revenueData" :key="month.name" class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ month.name }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ formatMoney(month.value) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                                             :style="{ width: maxRevenue > 0 ? (month.value / maxRevenue * 100) + '%' : '0%' }"></div>
                                    </div>
                                </div>
                                <div v-if="revenueData.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    No revenue data available
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Attorney Performance</h3>
                            <div class="space-y-4 max-h-80 overflow-y-auto">
                                <div v-for="attorney in stats.attorneyStats" :key="attorney.attorney.id"
                                     class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ attorney.attorney.name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ attorney.total_cases }} cases • {{ attorney.total_hours.toFixed(1) }} hours
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ formatMoney(attorney.total_revenue) }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatMoney(attorney.avg_hourly_rate) }}/hr
                                        </p>
                                    </div>
                                </div>
                                <div v-if="stats.attorneyStats.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    No attorney performance data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Clients -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Clients by Revenue</h3>
                                <button @click="exportReport" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cases</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Spent</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="client in stats.clientStats" :key="client.client.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ client.client.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ client.total_cases }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ client.total_hours.toFixed(1) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ formatMoney(client.total_spent) }}
                                        </td>
                                    </tr>
                                    <tr v-if="stats.clientStats.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No client data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Financial Report -->
                <div v-if="reportType === 'financial'" class="space-y-6">
                    <!-- Filters -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <form @submit.prevent="loadReportData" class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input v-model="financialFilters.start_date" type="date"
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input v-model="financialFilters.end_date" type="date"
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Apply Filters
                            </button>
                        </form>
                    </div>

                    <!-- Revenue by Period -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue by Period</h3>
                                <button @click="exportReport" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="data in reportData.revenueByPeriod" :key="data.date" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ new Date(data.date).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ formatMoney(data.revenue) }}
                                        </td>
                                    </tr>
                                    <tr v-if="reportData.revenueByPeriod.length === 0">
                                        <td colspan="2" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No revenue data found for the selected period
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Cases Report -->
                <div v-if="reportType === 'cases'" class="space-y-6">
                    <!-- Filters -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <form @submit.prevent="loadReportData" class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Case Status</label>
                                <select v-model="caseFilters.status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">All Statuses</option>
                                    <option value="1">Open</option>
                                    <option value="2">Pending</option>
                                    <option value="3">Closed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attorney</label>
                                <select v-model="caseFilters.attorney" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">All Attorneys</option>
                                    <!-- Attorney options would be populated from API -->
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Apply Filters
                            </button>
                        </form>
                    </div>

                    <!-- Case Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-blue-100 text-sm font-medium">Total Cases</p>
                                    <p class="text-2xl font-bold mt-1">{{ reportData.cases?.length || 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-green-100 text-sm font-medium">Total Hours</p>
                                    <p class="text-2xl font-bold mt-1">{{ (reportData.cases?.reduce((sum, c) => sum + (c.total_hours || 0), 0) || 0).toFixed(1) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-purple-100 text-sm font-medium">Total Billed</p>
                                    <p class="text-2xl font-bold mt-1">{{ formatMoney(reportData.cases?.reduce((sum, c) => sum + (c.total_billed || 0), 0) || 0) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-yellow-100 text-sm font-medium">Outstanding</p>
                                    <p class="text-2xl font-bold mt-1">{{ formatMoney(reportData.cases?.reduce((sum, c) => sum + (c.outstanding || 0), 0) || 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cases Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Case Details</h3>
                                <button @click="exportReport" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Case</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Attorney</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Billed</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paid</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="caseData in reportData.cases" :key="caseData.case.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ caseData.case.case_number }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ caseData.case.title }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ caseData.case.client?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ caseData.case.assignedAttorney?.name || 'Unassigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ caseData.case.status?.name || 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ caseData.total_hours?.toFixed(1) || 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ formatMoney(caseData.total_billed || 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                                            {{ formatMoney(caseData.total_paid || 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 dark:text-yellow-400">
                                            {{ formatMoney(caseData.outstanding || 0) }}
                                        </td>
                                    </tr>
                                    <tr v-if="reportData.cases?.length === 0">
                                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No cases found matching the selected filters
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const error = ref(null);
const activityFilter = ref('all');
const activities = ref([]);
const loadingActivities = ref(false);

const loadActivities = async () => {
    loadingActivities.value = true;
    try {
        const response = await axios.get('/api/activities', { params: { limit: 20 } });
        activities.value = response.data.activities || [];
    } catch (error) {
        console.error('Failed to load activities:', error);
    } finally {
        loadingActivities.value = false;
    }
};

const filteredActivities = computed(() => {
    if (activityFilter.value === 'all') return activities.value;
    return activities.value.filter(a => a.type === activityFilter.value);
});

// Filters
const financialFilters = reactive({
    start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
    end_date: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().split('T')[0]
});

const caseFilters = reactive({
    status: '',
    attorney: ''
});

// Computed properties
const stats = computed(() => ({
    totalRevenue: reportData.value.totalRevenue,
    outstandingAmount: reportData.value.outstandingAmount,
    overdueAmount: reportData.value.overdueAmount,
    activeCases: reportData.value.casesWithBilling?.length || 0,
    attorneyStats: reportData.value.attorneyStats,
    clientStats: reportData.value.clientStats
}));

const revenueData = computed(() => {
    // Convert monthly revenue data to chart format
    return reportData.value.monthlyRevenue?.slice(0, 12).reverse().map(item => ({
        name: `${item.year}-${String(item.month).padStart(2, '0')}`,
        value: parseFloat(item.revenue) || 0
    })) || [];
});

const maxRevenue = computed(() => {
    return Math.max(...revenueData.value.map(r => r.value), 1);
});

const casesByStatus = computed(() => {
    const statusCounts = {};
    reportData.value.cases?.forEach(caseItem => {
        const status = caseItem.case?.status?.name || 'Unknown';
        statusCounts[status] = (statusCounts[status] || 0) + 1;
    });

    return Object.entries(statusCounts).map(([name, count]) => ({
        name,
        count,
        color: getStatusColor(name)
    }));
});

// Helper functions
const getStatusColor = (status) => {
    const colors = {
        'Open': 'bg-green-500',
        'Active': 'bg-blue-500',
        'Pending': 'bg-yellow-500',
        'Completed': 'bg-green-500',
        'Closed': 'bg-gray-500',
        'On Hold': 'bg-gray-500'
    };
    return colors[status] || 'bg-gray-500';
};

const formatTime = (dateStr) => {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / 3600000);
    if (hours < 1) return 'Just now';
    if (hours < 24) return `${hours}h ago`;
    return date.toLocaleDateString();
};

const formatMoney = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0);
};

// API calls
const loadOverviewData = async () => {
    try {
        const response = await axios.get('/reports');
        reportData.value = { ...reportData.value, ...response.data };
    } catch (err) {
        console.error('Failed to load overview data:', err);
        error.value = 'Failed to load report data';
    }
};

const loadFinancialData = async () => {
    try {
        const params = new URLSearchParams(financialFilters);
        const response = await axios.get(`/reports/financial?${params}`);
        reportData.value = { ...reportData.value, ...response.data };
    } catch (err) {
        console.error('Failed to load financial data:', err);
        error.value = 'Failed to load financial report data';
    }
};

const loadCasesData = async () => {
    try {
        const params = new URLSearchParams(caseFilters);
        const response = await axios.get(`/reports/cases?${params}`);
        reportData.value.cases = response.data.cases || [];
    } catch (err) {
        console.error('Failed to load cases data:', err);
        error.value = 'Failed to load cases report data';
    }
};

const loadReportData = async () => {
    loading.value = true;
    error.value = null;

    try {
        if (reportType.value === 'overview') {
            await loadOverviewData();
        } else if (reportType.value === 'financial') {
            await loadFinancialData();
        } else if (reportType.value === 'cases') {
            await loadCasesData();
        }
    } catch (err) {
        error.value = err.message || 'Failed to load report data';
    } finally {
        loading.value = false;
    }
};

const exportReport = async () => {
    try {
        const params = new URLSearchParams({
            type: reportType.value,
            format: 'csv',
            ...financialFilters,
            ...caseFilters
        });

        const response = await axios.get(`/reports/export?${params}`, {
            responseType: 'blob'
        });

        // Create download link
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `report-${reportType.value}-${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (err) {
        console.error('Failed to export report:', err);
        alert('Failed to export report. Please try again.');
    }
};

const changeReportType = (type) => {
    reportType.value = type;
    router.push({ query: { type } });
};

// Watch for route changes
watch(() => route.query.type, (newType) => {
    if (newType && ['overview', 'financial', 'cases'].includes(newType)) {
        reportType.value = newType;
        loadReportData();
    }
});

// Initialize
onMounted(() => {
    const queryType = route.query.type;
    if (queryType && ['overview', 'financial', 'cases'].includes(queryType)) {
        reportType.value = queryType;
    }
    loadReportData();
    loadActivities();
});

// Watch filters
watch(financialFilters, () => {
    if (reportType.value === 'financial') {
        loadReportData();
    }
}, { deep: true });

watch(caseFilters, () => {
    if (reportType.value === 'cases') {
        loadReportData();
    }
}, { deep: true });
</script>
