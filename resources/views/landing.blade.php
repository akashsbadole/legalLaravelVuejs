<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('LegalFirm - Complete Legal Practice Management') }}</title>
    <meta name="description" content="Comprehensive legal practice management system with case management, client portal, billing, and AI assistance.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .stat-card:hover {
            transform: scale(105%);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-center">
                        <span class="text-white font-bold">L</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">LegalFirm</span>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">Dashboard</a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.onboarding') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">Setup</a>
                        @endif
                        <a href="{{ route('logout') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Logout</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Complete Legal Practice<br>
                    <span class="text-yellow-300">Management System</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                    Streamline your legal practice with comprehensive case management, client portals, GST-compliant billing, and AI-powered insights.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold text-lg hover:bg-gray-100 transition transform hover:scale-105">
                        Start Free Trial
                    </a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition">
                        Explore Features
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center stat-card transition duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ \App\Models\LegalCase::count() }}</div>
                    <div class="text-gray-600">Cases Managed</div>
                </div>

                <div class="text-center stat-card transition duration-300">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'client')->count() }}</div>
                    <div class="text-gray-600">Happy Clients</div>
                </div>

                <div class="text-center stat-card transition duration-300">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">₹{{ number_format(\App\Models\Invoice::sum('total_amount'), 0) }}</div>
                    <div class="text-gray-600">Revenue Generated</div>
                </div>

                <div class="text-center stat-card transition duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">99.9%</div>
                    <div class="text-gray-600">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Powerful Features for Modern Legal Practices
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage your legal practice efficiently, from case management to client communication.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Case Management -->
                <div class="feature-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Case Management</h3>
                    <p class="text-gray-600 mb-4">Complete case lifecycle management with timeline tracking, document association, and automated workflows.</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Case creation & assignment</li>
                        <li>• Timeline & activity tracking</li>
                        <li>• Document management</li>
                        <li>• Status & deadline monitoring</li>
                    </ul>
                </div>

                <!-- Client Portal -->
                <div class="feature-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition duration-300">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Client Portal</h3>
                    <p class="text-gray-600 mb-4">Secure client access to case information, documents, invoices, and communication history.</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Secure login & authentication</li>
                        <li>• Case status & updates</li>
                        <li>• Document downloads</li>
                        <li>• Billing & payment tracking</li>
                    </ul>
                </div>

                <!-- Billing & Invoicing -->
                <div class="feature-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition duration-300">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">GST-Compliant Billing</h3>
                    <p class="text-gray-600 mb-4">Automated invoicing with GST calculations, time tracking, and expense management.</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Time & expense tracking</li>
                        <li>• Automatic GST calculation</li>
                        <li>• Invoice generation</li>
                        <li>• Payment processing</li>
                    </ul>
                </div>

                <!-- Communication -->
                <div class="feature-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Communication Hub</h3>
                    <p class="text-gray-600 mb-4">Centralized communication with call logging, email tracking, and follow-up management.</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Call logging & tracking</li>
                        <li>• Email communication</li>
                        <li>• Follow-up scheduling</li>
                        <li>• Communication history</li>
                    </ul>
                </div>

                <!-- Role-Based Access -->
                <div class="feature-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition duration-300">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Role-Based Security</h3>
                    <p class="text-gray-600 mb-4">Granular permissions and access control for attorneys, paralegals, clients, and administrators.</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Attorney permissions</li>
                        <li>• Paralegal access levels</li>
                        <li>• Client portal security</li>
                        <li>• Admin oversight</li>
                    </ul>
                </div>

                <!-- AI Assistance -->
                <div class="feature-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition duration-300">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">AI-Powered Insights</h3>
                    <p class="text-gray-600 mb-4">Smart analytics and AI assistance for case outcome prediction and legal research support.</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Case outcome prediction</li>
                        <li>• Legal research assistance</li>
                        <li>• Document analysis</li>
                        <li>• Smart scheduling</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Enquiry Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Have Questions?</h2>
            <p class="text-xl text-gray-600 mb-8">Get in touch with our legal experts. We'll respond within 24 hours.</p>

            <div class="bg-gray-50 rounded-2xl p-8">
                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="name" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Service Needed</label>
                        <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select a service</option>
                            <option value="civil_law">Civil Law</option>
                            <option value="criminal_law">Criminal Law</option>
                            <option value="corporate_law">Corporate Law</option>
                            <option value="family_law">Family Law</option>
                            <option value="property_law">Property Law</option>
                            <option value="consultation">General Consultation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Urgency</label>
                        <select name="priority" id="priority" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea name="message" id="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Please describe your legal matter..." required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold text-lg hover:bg-blue-700 transition transform hover:scale-105">
                        Send Enquiry
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-center">
                            <span class="text-white font-bold">L</span>
                        </div>
                        <span class="text-xl font-bold">LegalFirm</span>
                    </div>
                    <p class="text-gray-400">Complete legal practice management for modern law firms.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Features</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Case Management</a></li>
                        <li><a href="#" class="hover:text-white transition">Client Portal</a></li>
                        <li><a href="#" class="hover:text-white transition">Billing & Invoicing</a></li>
                        <li><a href="#" class="hover:text-white transition">Document Management</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#features" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="#features" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#features" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <div class="text-gray-400 space-y-2">
                        <p>📧 support@legalfirm.com</p>
                        <p>📞 +91 98765 43210</p>
                        <p>📍 Mumbai, India</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} LegalFirm. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>