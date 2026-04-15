<template>
    <div class="font-sans antialiased bg-white text-slate-900">
        <!-- WhatsApp Floating Button - Right Side -->
        <a href="https://wa.me/7219079546?text=Hello, I want to know about legal web application, can you please share more information" target="_blank"
           class="fixed bottom-6 right-6 w-14 h-14 bg-[#25d366] text-white rounded-full flex items-center justify-center shadow-lg hover:bg-[#20c35e] hover:scale-110 transition-all duration-300 z-50"
           style="box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);">
            <i class="fab fa-whatsapp text-2xl"></i>
        </a>

        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">L</span>
                        </div>
                        <span class="text-xl font-bold text-blue-600">LegalFirm</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- <a href="/features" class="px-3 py-2 text-slate-600 hover:text-blue-600 text-sm font-medium transition">Features</a>
                        <a href="/contact" class="px-3 py-2 text-slate-600 hover:text-blue-600 text-sm font-medium transition">Contact</a> -->
                        <a href="/#/login" class="px-4 py-2 text-slate-600 hover:text-blue-600 text-sm font-medium transition">Login</a>
                        <a href="/#/register" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Get Started</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="bg-blue-600 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center px-3 py-1 bg-blue-700 rounded-full mb-6">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                        <span class="text-sm text-blue-100">Trusted by 500+ Law Firms Worldwide</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                        Complete Legal Practice<br>Management System
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                        Streamline your legal practice with comprehensive case management, client portals,
                        GST-compliant billing, AI-powered insights, and end-to-end document automation.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/register" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-gray-100 transition">
                            Start Free Trial
                        </a>
                        <a href="#features" class="px-8 py-4 border-2 border-white text-white rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition">
                            Explore Features
                        </a>
                    </div>
                    <div class="mt-10 flex flex-wrap justify-center items-center gap-6 text-blue-200">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-300"></i>
                            <span class="text-sm">ISO 27001</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-lock text-blue-300"></i>
                            <span class="text-sm">Bank-Grade Security</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-headset text-blue-300"></i>
                            <span class="text-sm">24/7 Support</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-cloud text-blue-300"></i>
                            <span class="text-sm">99.9% Uptime</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center p-6 bg-blue-50 rounded-xl">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-folder-open text-white"></i>
                        </div>
                        <div class="text-3xl font-bold text-blue-600">{{ stats.cases }}</div>
                        <div class="text-sm text-slate-600">Cases Managed</div>
                    </div>
                    <div class="text-center p-6 bg-blue-50 rounded-xl">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div class="text-3xl font-bold text-blue-600">{{ stats.clients }}</div>
                        <div class="text-sm text-slate-600">Happy Clients</div>
                    </div>
                    <div class="text-center p-6 bg-blue-50 rounded-xl">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-rupee-sign text-white"></i>
                        </div>
                        <div class="text-3xl font-bold text-blue-600">{{ stats.revenue }}</div>
                        <div class="text-sm text-slate-600">Revenue</div>
                    </div>
                    <div class="text-center p-6 bg-blue-50 rounded-xl">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div class="text-3xl font-bold text-blue-600">99.9%</div>
                        <div class="text-sm text-slate-600">Uptime</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Powerful Features</h2>
                    <p class="text-lg text-slate-600 max-w-2xl mx-auto">Everything you need to manage your legal practice efficiently.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="feature in features" :key="feature.title" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                            <i :class="feature.icon" class="text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-slate-600 text-sm mb-3">{{ feature.description }}</p>
                        <ul class="space-y-1">
                            <li v-for="item in feature.items" :key="item" class="flex items-center text-xs text-slate-500">
                                <i class="fas fa-check text-green-500 mr-2"></i>{{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">How It Works</h2>
                    <p class="text-lg text-slate-600">Get started in 3 simple steps</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Sign Up</h3>
                        <p class="text-slate-600">Create your account in seconds</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Configure</h3>
                        <p class="text-slate-600">Set up your firm details and users</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white">3</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Go Live</h3>
                        <p class="text-slate-600">Start managing your cases immediately</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-blue-600">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Transform Your Practice?</h2>
                <p class="text-lg text-blue-100 mb-8">Join hundreds of law firms already using LegalFirm.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-gray-100 transition">
                        Start Free Trial
                    </a>
                    <a href="#contact" class="px-8 py-4 border-2 border-white text-white rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition">
                        Contact Sales
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-slate-50">
            <div class="max-w-2xl mx-auto px-4">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Have Questions?</h2>
                    <p class="text-slate-600">Get in touch with our legal experts.</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-8 border border-slate-200">
                    <form @submit.prevent="submitEnquiry" class="space-y-5">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                                <input type="text" v-model="form.name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                <input type="email" v-model="form.email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="john@example.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                            <input type="tel" v-model="form.phone" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="+91 98765 43210">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Service Needed</label>
                            <select v-model="form.category" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a service</option>
                                <option value="civil_law">Civil Law</option>
                                <option value="criminal_law">Criminal Law</option>
                                <option value="corporate_law">Corporate Law</option>
                                <option value="family_law">Family Law</option>
                                <option value="property_law">Property Law</option>
                                <option value="consultation">General Consultation</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                            <textarea v-model="form.message" rows="4" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe your legal matter..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i>Send Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                <span class="text-white font-bold text-lg">L</span>
                            </div>
                            <span class="text-xl font-bold">LegalFirm</span>
                        </div>
                        <p class="text-slate-400 mb-4 max-w-md">Complete legal practice management for modern law firms.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://wa.me/7219079546?text=Hello, I want to know about legal web application, can you please share more information" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">Features</h3>
                        <ul class="space-y-2 text-slate-400">
                            <li><a href="#features" class="hover:text-white">Case Management</a></li>
                            <li><a href="#features" class="hover:text-white">Client Portal</a></li>
                            <li><a href="#features" class="hover:text-white">Billing</a></li>
                            <li><a href="#features" class="hover:text-white">AI Insights</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">Contact</h3>
                        <ul class="space-y-2 text-slate-400">
                            <li class="flex items-center gap-2"><i class="fas fa-envelope"></i> support@legalfirm.com</li>
                            <li class="flex items-center gap-2"><i class="fas fa-phone"></i> +91 98765 43210</li>
                            <li class="flex items-center gap-2"><i class="fas fa-map-marker-alt"></i> Mumbai, India</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-slate-500 text-sm">&copy; {{ new Date().getFullYear() }} LegalFirm. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const form = ref({
    name: '',
    email: '',
    phone: '',
    category: '',
    message: ''
});

const stats = ref({
    cases: 156,
    clients: 89,
    revenue: '₹24,50,000'
});

const features = [
    { title: 'Case Management', description: 'Complete case lifecycle management with timeline tracking and workflows.', icon: 'fas fa-briefcase', items: ['Case creation', 'Timeline tracking', 'Document management'] },
    { title: 'Client Portal', description: 'Secure client access to case information and documents.', icon: 'fas fa-user-shield', items: ['Secure login', 'Case status', 'Document downloads'] },
    { title: 'GST-Compliant Billing', description: 'Automated invoicing with GST calculations.', icon: 'fas fa-file-invoice-dollar', items: ['Time tracking', 'GST calculation', 'Invoice generation'] },
    { title: 'Communication Hub', description: 'Centralized communication with call logging.', icon: 'fas fa-comments', items: ['Call logging', 'Email tracking', 'Follow-ups'] },
    { title: 'Role-Based Security', description: 'Granular permissions for team members.', icon: 'fas fa-shield-alt', items: ['Attorney access', 'Paralegal access', 'Admin controls'] },
    { title: 'AI-Powered Insights', description: 'Smart analytics and AI assistance.', icon: 'fas fa-robot', items: ['Case prediction', 'Legal research', 'Smart scheduling'] },
    { title: 'Document Automation', description: 'Generate legal documents automatically.', icon: 'fas fa-file-signature', items: ['Templates', 'E-signatures', 'Auto-fill'] },
    { title: 'Trust Accounting', description: 'Compliance-focused trust accounting.', icon: 'fas fa-university', items: ['Trust funds', 'Ledgers', 'Reconciliation'] },
    { title: 'Legal Research', description: 'Built-in access to IPC, CPC, CRPC.', icon: 'fas fa-gavel', items: ['IPC sections', 'Case judgments', 'State laws'] },
    { title: 'Lead Management', description: 'Convert inquiries into clients.', icon: 'fas fa-user-plus', items: ['Lead capture', 'Tracking', 'Pipeline'] },
    { title: 'Task Management', description: 'Assign tasks and track progress.', icon: 'fas fa-tasks', items: ['Task assignment', 'Due dates', 'Priority levels'] },
    { title: 'Multi-Firm Support', description: 'Manage multiple firms.', icon: 'fas fa-building', items: ['Multi-tenant', 'Branches', 'Unified reports'] }
];

const submitEnquiry = async () => {
    try {
        const response = await fetch('/contact/submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
            body: JSON.stringify(form.value)
        });
        if (response.ok) {
            alert('Enquiry sent successfully!');
            form.value = { name: '', email: '', phone: '', category: '', message: '' };
        }
    } catch (e) {
        alert('Error sending enquiry');
    }
};
</script>
