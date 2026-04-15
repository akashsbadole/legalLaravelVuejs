<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LegalFirm - Complete Legal Practice Management Software</title>
    <meta name="description" content="Comprehensive legal practice management system with case management, client portal, billing, AI assistance, document automation, and trust accounting.">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div id="app" x-data="landingApp()">
        <!-- WhatsApp Floating Button -->
        <a href="https://wa.me/917219079546" target="_blank"
           class="fixed bottom-6 right-6 w-14 h-14 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 hover:scale-110 transition-all duration-300 z-50"
           style="box-shadow: 0 4px 20px rgba(34, 197, 94, 0.4);">
            <i class="fab fa-whatsapp text-2xl"></i>
        </a>

        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-200/50 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">L</span>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">LegalFirm</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        @auth
                            <a href="{{ url('/app') }}" class="px-4 py-2 text-slate-600 hover:text-blue-600 rounded-lg text-sm font-medium transition">Dashboard</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="/admin/onboarding" class="px-4 py-2 text-slate-600 hover:text-blue-600 rounded-lg text-sm font-medium transition">Setup</a>
                            @endif
                            <form method="POST" action="/logout" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Logout</button>
                            </form>
                        @else
                            <a href="/login" class="px-4 py-2 text-slate-600 hover:text-blue-600 rounded-lg text-sm font-medium transition">Login</a>
                            <a href="/register" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:scale-105 transition-all duration-300">Get Started</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
            <!-- Animated Background -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-indigo-500/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.03&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-8 border border-white/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm text-blue-100 font-medium">Trusted by 500+ Law Firms Worldwide</span>
                </div>

                <!-- Hero Content -->
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                    Complete Legal Practice<br>
                    <span class="bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent">Management System</span>
                </h1>

                <p class="text-xl md:text-2xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed">
                    Streamline your legal practice with comprehensive case management, client portals,
                    GST-compliant billing, AI-powered insights, and end-to-end document automation.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <a href="/register" class="px-8 py-4 bg-white text-indigo-600 rounded-xl font-semibold text-lg hover:bg-slate-100 transition-all duration-300 hover:shadow-2xl hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-rocket"></i>Start Free Trial
                    </a>
                    <a href="#features" class="px-8 py-4 border-2 border-white/30 text-white rounded-xl font-semibold text-lg hover:bg-white/10 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i>Explore Features
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="flex flex-wrap justify-center items-center gap-8 text-blue-200">
                    <div class="flex items-center gap-2 bg-white/5 px-4 py-2 rounded-full">
                        <i class="fas fa-shield-alt text-yellow-400"></i>
                        <span class="text-sm">ISO 27001</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 px-4 py-2 rounded-full">
                        <i class="fas fa-lock text-green-400"></i>
                        <span class="text-sm">Bank-Grade Security</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 px-4 py-2 rounded-full">
                        <i class="fas fa-headset text-blue-400"></i>
                        <span class="text-sm">24/7 Support</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 px-4 py-2 rounded-full">
                        <i class="fas fa-cloud text-purple-400"></i>
                        <span class="text-sm">99.9% Uptime</span>
                    </div>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
                <i class="fas fa-chevron-down text-white/50 text-2xl"></i>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-white relative -mt-20 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <template x-for="stat in stats" :key="stat.label">
                        <div class="bg-gradient-to-br rounded-2xl p-6 text-center shadow-xl hover:scale-105 transition-transform duration-300"
                             :class="stat.bgClass">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg"
                                 :class="stat.iconBgClass">
                                <i :class="stat.icon" class="text-xl text-white"></i>
                            </div>
                            <div class="text-3xl lg:text-4xl font-bold mb-1" x-text="stat.value"></div>
                            <div class="font-medium" :class="stat.textClass" x-text="stat.label"></div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-star mr-2"></i>Powerful Features
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">
                        Everything You Need
                    </h2>
                    <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                        From case management to AI-powered insights, we provide all the tools to streamline your firm's operations.
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="(feature, index) in features" :key="feature.title">
                        <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100"
                             :style="`animation-delay: ${index * 100}ms`">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-5 shadow-lg transition-transform group-hover:scale-110"
                                 :class="feature.iconBgClass">
                                <i :class="feature.icon" class="text-xl text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3" x-text="feature.title"></h3>
                            <p class="text-slate-600 mb-5" x-text="feature.description"></p>
                            <ul class="space-y-2">
                                <template x-for="item in feature.items" :key="item">
                                    <li class="flex items-center text-sm text-slate-600">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        <span x-text="item"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-question-circle mr-2"></i>How It Works
                    </span>
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Get Started in 3 Steps</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <template x-for="(step, index) in steps" :key="step.title">
                        <div class="text-center relative">
                            <div class="w-20 h-20 bg-gradient-to-br rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl"
                                 :class="step.bgClass">
                                <span class="text-3xl font-bold text-white" x-text="index + 1"></span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2" x-text="step.title"></h3>
                            <p class="text-slate-600" x-text="step.description"></p>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Law Resources Section -->
        <section id="law-resources" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-gavel mr-2"></i>Law Resources
                    </span>
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Legal Documents & Resources</h2>
                    <p class="text-xl text-slate-600">Access important legal documents, codes, and reference materials</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- PDF Viewer -->
                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-file-pdf text-red-500"></i> Indian Constitution Preview
                        </h3>
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                            <iframe 
                                src="/data/INDIA.pdf" 
                                class="w-full h-[400px]"
                                title="India PDF Viewer">
                            </iframe>
                        </div>
                        <div class="mt-4 flex gap-3">
                            <a href="/data/INDIA.pdf" download 
                               class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-center font-medium hover:bg-blue-700 transition">
                                <i class="fas fa-download mr-2"></i>Download Full Document
                            </a>
                        </div>
                    </div>

                    <!-- Law Files Download -->
                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-archive text-blue-500"></i> Law Files Library
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Indian Penal Code (IPC)</p>
                                        <p class="text-xs text-slate-500">Full code with all sections</p>
                                    </div>
                                </div>
                                <a href="/data/INDIA.pdf" download class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-alt text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Code of Criminal Procedure</p>
                                        <p class="text-xs text-slate-500">CRPC complete reference</p>
                                    </div>
                                </div>
                                <a href="/data/INDIA.pdf" download class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-balance-scale text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Civil Procedure Code</p>
                                        <p class="text-xs text-slate-500">CPC complete guide</p>
                                    </div>
                                </div>
                                <a href="/data/INDIA.pdf" download class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-users text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Indian Evidence Act</p>
                                        <p class="text-xs text-slate-500">Evidence law reference</p>
                                    </div>
                                </div>
                                <a href="/data/INDIA.pdf" download class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-home text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Property Laws</p>
                                        <p class="text-xs text-slate-500">Property act references</p>
                                    </div>
                                </div>
                                <a href="/data/INDIA.pdf" download class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-heart text-pink-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Family Laws</p>
                                        <p class="text-xs text-slate-500">Hindu Marriage Act, etc.</p>
                                    </div>
                                </div>
                                <a href="/data/INDIA.pdf" download class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 relative overflow-hidden">
            <div class="absolute inset-0">
                <div class="absolute top-0 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            </div>
            <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Ready to Transform Your Practice?</h2>
                <p class="text-xl text-blue-100 mb-10">Join hundreds of law firms already using LegalFirm to streamline their operations.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="px-8 py-4 bg-white text-indigo-600 rounded-xl font-semibold text-lg hover:bg-slate-100 transition-all duration-300 hover:shadow-2xl hover:scale-105">
                        Start Free Trial
                    </a>
                    <a href="#features" class="px-8 py-4 border-2 border-white text-white rounded-xl font-semibold text-lg hover:bg-white/10 transition-all duration-300">
                        Schedule Demo
                    </a>
                </div>
            </div>
        </section>

        <!-- Enquiry Section -->
        <section class="py-24 bg-slate-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-envelope mr-2"></i>Get In Touch
                    </span>
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Have Questions?</h2>
                    <p class="text-xl text-slate-600">We'll respond within 24 hours.</p>
                </div>

                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-slate-100">
                    <form method="POST" action="/contact/submit" class="space-y-5" @submit="showSuccess = true">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                                <input type="text" name="name" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                                <input type="email" name="email" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       placeholder="john@example.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                            <input type="tel" name="phone"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                   placeholder="+91 98765 43210">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Service Needed</label>
                            <select name="category" required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition appearance-none bg-white">
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
                            <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                            <textarea name="message" rows="4" required
                                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                      placeholder="Describe your legal matter..."></textarea>
                        </div>

                        <button type="submit"
                                class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold text-lg hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                            <i class="fas fa-paper-plane mr-2"></i>Send Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-10">
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-xl">L</span>
                            </div>
                            <span class="text-2xl font-bold">LegalFirm</span>
                        </div>
                        <p class="text-slate-400 mb-6 max-w-md">Complete legal practice management for modern law firms.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://wa.me/917219079546" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Features</h3>
                        <ul class="space-y-3 text-slate-400">
                            <li><a href="#features" class="hover:text-white transition">Case Management</a></li>
                            <li><a href="#features" class="hover:text-white transition">Client Portal</a></li>
                            <li><a href="#features" class="hover:text-white transition">Billing</a></li>
                            <li><a href="#features" class="hover:text-white transition">AI Insights</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact</h3>
                        <ul class="space-y-3 text-slate-400">
                            <li class="flex items-center gap-3"><i class="fas fa-envelope"></i> support@legalfirm.com</li>
                            <li class="flex items-center gap-3"><i class="fas fa-phone"></i> +91 98765 43210</li>
                            <li class="flex items-center gap-3"><i class="fas fa-map-marker-alt"></i> Mumbai, India</li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-800 mt-10 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} LegalFirm. All rights reserved.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-slate-500 hover:text-white text-sm transition">Privacy</a>
                        <a href="#" class="text-slate-500 hover:text-white text-sm transition">Terms</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function landingApp() {
            return {
                showSuccess: false,
                stats: [
                    {
                        value: '{{ \App\Models\LegalCase::count() }}',
                        label: 'Cases Managed',
                        icon: 'fas fa-folder-open',
                        bgClass: 'from-blue-50 to-blue-100 border border-blue-200',
                        iconBgClass: 'bg-gradient-to-br from-blue-500 to-blue-600',
                        textClass: 'text-blue-600'
                    },
                    {
                        value: '{{ \App\Models\User::where("role", "client")->count() }}',
                        label: 'Happy Clients',
                        icon: 'fas fa-users',
                        bgClass: 'from-green-50 to-green-100 border border-green-200',
                        iconBgClass: 'bg-gradient-to-br from-green-500 to-green-600',
                        textClass: 'text-green-600'
                    },
                    {
                        value: '₹{{ number_format(\App\Models\Invoice::sum("total_amount"), 0) }}',
                        label: 'Revenue',
                        icon: 'fas fa-rupee-sign',
                        bgClass: 'from-amber-50 to-amber-100 border border-amber-200',
                        iconBgClass: 'bg-gradient-to-br from-amber-500 to-orange-500',
                        textClass: 'text-amber-600'
                    },
                    {
                        value: '99.9%',
                        label: 'Uptime',
                        icon: 'fas fa-chart-line',
                        bgClass: 'from-purple-50 to-purple-100 border border-purple-200',
                        iconBgClass: 'bg-gradient-to-br from-purple-500 to-purple-600',
                        textClass: 'text-purple-600'
                    }
                ],
                features: [
                    {
                        title: 'Case Management',
                        description: 'Complete case lifecycle management with timeline tracking and automated workflows.',
                        icon: 'fas fa-briefcase',
                        iconBgClass: 'bg-gradient-to-br from-blue-500 to-blue-600',
                        items: ['Case creation & assignment', 'Timeline tracking', 'Document management', 'Deadline monitoring']
                    },
                    {
                        title: 'Client Portal',
                        description: 'Secure client access to case information, documents, and invoices.',
                        icon: 'fas fa-user-shield',
                        iconBgClass: 'bg-gradient-to-br from-green-500 to-green-600',
                        items: ['Secure authentication', 'Case status updates', 'Document downloads', 'Payment tracking']
                    },
                    {
                        title: 'GST-Compliant Billing',
                        description: 'Automated invoicing with GST calculations and expense management.',
                        icon: 'fas fa-file-invoice-dollar',
                        iconBgClass: 'bg-gradient-to-br from-amber-500 to-orange-500',
                        items: ['Time & expense tracking', 'GST calculation', 'Invoice generation', 'Payment processing']
                    },
                    {
                        title: 'Communication Hub',
                        description: 'Centralized communication with call logging and follow-up management.',
                        icon: 'fas fa-comments',
                        iconBgClass: 'bg-gradient-to-br from-purple-500 to-purple-600',
                        items: ['Call logging', 'Email tracking', 'Follow-up scheduling', 'History']
                    },
                    {
                        title: 'Role-Based Security',
                        description: 'Granular permissions for attorneys, paralegals, clients, and admins.',
                        icon: 'fas fa-shield-alt',
                        iconBgClass: 'bg-gradient-to-br from-indigo-500 to-indigo-600',
                        items: ['Attorney permissions', 'Paralegal access', 'Client portal security', 'Admin oversight']
                    },
                    {
                        title: 'AI-Powered Insights',
                        description: 'Smart analytics and AI assistance for case outcomes.',
                        icon: 'fas fa-robot',
                        iconBgClass: 'bg-gradient-to-br from-pink-500 to-rose-500',
                        items: ['Case prediction', 'Legal research', 'Document analysis', 'Smart scheduling']
                    },
                    {
                        title: 'Document Automation',
                        description: 'Generate legal documents with templates and e-signatures.',
                        icon: 'fas fa-file-signature',
                        iconBgClass: 'bg-gradient-to-br from-cyan-500 to-teal-500',
                        items: ['Template management', 'E-signatures', 'Auto-fill', 'Version control']
                    },
                    {
                        title: 'Trust Accounting',
                        description: 'Compliance-focused trust accounting with reconciliation.',
                        icon: 'fas fa-university',
                        iconBgClass: 'bg-gradient-to-br from-emerald-500 to-green-500',
                        items: ['Trust fund management', 'Ledger tracking', 'Reconciliation', 'Compliance reports']
                    },
                    {
                        title: 'Legal Research',
                        description: 'Built-in access to IPC, CPC, CRPC sections and more.',
                        icon: 'fas fa-gavel',
                        iconBgClass: 'bg-gradient-to-br from-violet-500 to-purple-600',
                        items: ['IPC, CPC, CRPC', 'State laws', 'Constitutional articles', 'Case judgments']
                    },
                    {
                        title: 'Lead Management',
                        description: 'Convert inquiries into clients with automated workflows.',
                        icon: 'fas fa-user-plus',
                        iconBgClass: 'bg-gradient-to-br from-amber-500 to-yellow-500',
                        items: ['Lead capture', 'Tracking & status', 'Auto-conversion', 'Pipeline view']
                    },
                    {
                        title: 'Task Management',
                        description: 'Assign tasks, set deadlines, and track progress.',
                        icon: 'fas fa-tasks',
                        iconBgClass: 'bg-gradient-to-br from-orange-500 to-red-500',
                        items: ['Task assignment', 'Due dates', 'Priority levels', 'Recurring tasks']
                    },
                    {
                        title: 'Multi-Firm Support',
                        description: 'Manage multiple firms with tenant isolation.',
                        icon: 'fas fa-building',
                        iconBgClass: 'bg-gradient-to-br from-slate-500 to-gray-600',
                        items: ['Multi-tenant', 'Branch management', 'Cross-firm reports', 'Unified dashboard']
                    }
                ],
                steps: [
                    {
                        title: 'Sign Up',
                        description: 'Create your account in seconds with our simple registration process.',
                        bgClass: 'from-blue-500 to-indigo-500'
                    },
                    {
                        title: 'Configure',
                        description: 'Set up your firm details, users, and customize workflows.',
                        bgClass: 'from-green-500 to-emerald-500'
                    },
                    {
                        title: 'Go Live',
                        description: 'Start managing your cases and clients immediately.',
                        bgClass: 'from-purple-500 to-pink-500'
                    }
                ]
            }
        }
    </script>
</body>
</html>
