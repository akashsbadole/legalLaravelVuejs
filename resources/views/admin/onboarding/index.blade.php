<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Admin Setup - LegalFirm') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .onboarding-progress {
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        }
        .step-active {
            background: #3b82f6;
            color: white;
        }
        .step-completed {
            background: #10b981;
            color: white;
        }
        .step-inactive {
            background: #e5e7eb;
            color: #6b7280;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome to LegalFirm</h1>
                <p class="mt-2 text-gray-600">Let's set up your legal practice management system</p>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Setup Progress</span>
                        <span>{{ count($onboarding->completed_steps ?? []) }} of {{ count($onboarding->getSteps()) }} completed</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="onboarding-progress h-2 rounded-full transition-all duration-500"
                             style="width: {{ (count($onboarding->completed_steps ?? []) / count($onboarding->getSteps())) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Step Indicators -->
                <div class="flex justify-between mb-8">
                    @foreach($onboarding->getSteps() as $stepNumber => $step)
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium mb-2
                                {{ $onboarding->isStepCompleted($stepNumber) ? 'step-completed' :
                                   ($stepNumber === $onboarding->current_step ? 'step-active' : 'step-inactive') }}">
                                @if($onboarding->isStepCompleted($stepNumber))
                                    ✓
                                @else
                                    {{ $stepNumber }}
                                @endif
                            </div>
                            <div class="text-xs text-center max-w-20">
                                {{ $stepNumber === $onboarding->current_step ? $step['title'] : 'Step ' . $stepNumber }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Current Step Content -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $stepData['title'] }}</h2>
                    <p class="text-gray-600">{{ $stepData['description'] }}</p>
                </div>

                <!-- Step-Specific Content -->
                @switch($onboarding->current_step)
                    @case(1)
                        <!-- Welcome Step -->
                        <div class="bg-blue-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-blue-900 mb-4">🎉 Welcome to Your Legal Practice Management System!</h3>
                            <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-800">
                                <div class="flex items-center">
                                    <span class="mr-2">📋</span>
                                    <span>Complete case management</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">👥</span>
                                    <span>Client relationship management</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">💰</span>
                                    <span>GST-compliant billing</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">📅</span>
                                    <span>Automated scheduling</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">📄</span>
                                    <span>Document management</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">🔒</span>
                                    <span>PDPB compliance</span>
                                </div>
                            </div>
                        </div>
                        @break

                    @case(2)
                        <!-- Create Attorney Step -->
                        <div class="bg-green-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-green-900 mb-4">👨‍⚖️ Add Your First Attorney</h3>
                            <p class="text-green-700 mb-4">Attorneys are the backbone of your legal practice. Add your team members to get started.</p>

                            @if($context['attorneys_count'] > 0)
                                <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4">
                                    ✅ You already have {{ $context['attorneys_count'] }} attorney(s) in your system.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.onboarding.next') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="create_attorney" value="1">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <input type="text" name="name" id="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                </div>
                                <div class="md:w-1/2">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone (Optional)</label>
                                    <input type="text" name="phone" id="phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <p class="text-xs text-gray-500">Note: A temporary password will be set. The attorney can change it later.</p>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                        Add Attorney & Continue
                                    </button>
                                </div>
                            </form>
                        </div>
                        @break

                    @case(3)
                        <!-- Case Types Setup -->
                        <div class="bg-purple-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-purple-900 mb-4">📋 Configure Case Categories</h3>
                            <p class="text-purple-700 mb-4">Set up the types of cases your practice handles.</p>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-purple-900 mb-2">Current Case Types</h4>
                                    @if($context['case_types']->count() > 0)
                                        <ul class="space-y-1">
                                            @foreach($context['case_types'] as $type)
                                                <li class="flex items-center text-sm text-purple-700">
                                                    <span class="mr-2">✓</span>
                                                    {{ ucfirst($type) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-purple-600 text-sm">No case types configured yet. Create your first case to set this up.</p>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-medium text-purple-900 mb-2">Case Statuses</h4>
                                    <div class="space-y-2">
                                        @foreach($context['case_statuses'] as $status)
                                            <div class="flex items-center text-sm text-purple-700">
                                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $status->color ?? '#6b7280' }}"></div>
                                                {{ $status->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break

                    @case(4)
                        <!-- Billing Setup -->
                        <div class="bg-yellow-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-4">💰 Configure Billing Rates</h3>
                            <p class="text-yellow-700 mb-4">Set hourly rates for your attorneys and enable GST compliance.</p>

                            @if($context['attorneys']->count() > 0)
                                <form method="POST" action="{{ route('admin.onboarding.next') }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="update_rates" value="1">
                                    <div class="space-y-3">
                                        @foreach($context['attorneys'] as $attorney)
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                                <span class="font-medium text-gray-900">{{ $attorney->name }}</span>
                                                <div class="flex items-center">
                                                    <span class="text-gray-500 mr-2">₹</span>
                                                    <input type="number" name="hourly_rates[{{ $attorney->id }}]" step="0.01" min="0"
                                                           value="{{ $attorney->hourly_rate ?? 0 }}"
                                                           class="w-24 px-2 py-1 border border-gray-300 rounded text-right" placeholder="0.00">
                                                    <span class="text-gray-500 ml-2">/hour</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">GST will be automatically calculated at 18% for all invoices.</p>
                                    <div class="flex justify-end">
                                        <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                            Save Rates & Continue
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p class="text-yellow-600">Please add attorneys first before configuring billing rates.</p>
                            @endif
                        </div>
                        @break

                    @case(5)
                        <!-- Feature Tour -->
                        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-indigo-900 mb-4">🚀 Explore Your Features</h3>
                            <p class="text-indigo-700 mb-4">Your system is ready! Here's what you can do:</p>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-lg">
                                    <h4 class="font-medium text-indigo-900 mb-2">📊 Dashboard</h4>
                                    <p class="text-sm text-indigo-700">Monitor cases, tasks, and revenue at a glance.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <h4 class="font-medium text-indigo-900 mb-2">👥 Client Portal</h4>
                                    <p class="text-sm text-indigo-700">Secure access for clients to view their cases.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <h4 class="font-medium text-indigo-900 mb-2">📋 Case Management</h4>
                                    <p class="text-sm text-indigo-700">{{ $context['features']['case_management'] }} cases ready for management.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <h4 class="font-medium text-indigo-900 mb-2">💳 Billing</h4>
                                    <p class="text-sm text-indigo-700">{{ $context['features']['invoices'] }} invoices created.</p>
                                </div>
                            </div>
                        </div>
                        @break
                @endswitch

                <!-- Navigation Buttons -->
                <div class="flex justify-between">
                    <form method="POST" action="{{ route('admin.onboarding.skip') }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 text-gray-600 hover:text-gray-800 transition">
                            Skip Setup
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.onboarding.next') }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ $stepData['action'] }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>