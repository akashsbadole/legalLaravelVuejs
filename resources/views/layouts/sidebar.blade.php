<div class="fixed inset-y-0 left-0 z-50 w-60 sm:w-64 lg:w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transform transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none lg:translate-x-0" :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
    @php use Illuminate\Support\Facades\Auth; @endphp
    <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ url('/') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
                <span class="text-white font-bold">L</span>
            </div>
            <span class="text-lg font-semibold text-gray-900 dark:text-white">LegalFirm</span>
        </a>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-4">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Main</p>
            <a href="{{ url('/') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.dashboard') }}</span>
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Management</p>
            @if(!Auth::user()->isClient())
            <a href="{{ route('cases.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('cases.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.cases') }}</span>
            </a>
            <a href="{{ route('tasks.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.tasks') }}</span>
            </a>
            <a href="{{ route('documents.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('documents.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.documents') }}</span>
            </a>
            <a href="{{ route('billing.invoices') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('billing.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.billing') }}</span>
            </a>
            <a href="{{ route('trust-accounts.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('trust-accounts.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Trust Accounts</span>
            </a>
            <a href="{{ route('calendar.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('calendar.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Calendar</span>
            </a>
            <a href="{{ route('crm.dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('crm.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>CRM</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Reports</span>
            </a>
            @endif
            <a href="{{ route('search') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('search') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Search</span>
            </a>
            @if(Auth::user() && Auth::user()->isAdmin())
            <a href="{{ route('admin.tenants.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.tenants.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Tenants</span>
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.audit-logs.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Audit Logs</span>
            </a>
            @endif
            <a href="{{ route('notifications.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('notifications.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Notifications</span>
            </a>
            @if(Auth::user()->isClient())
            <a href="{{ route('cases.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('cases.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.cases') }}</span>
            </a>
            <a href="{{ route('documents.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('documents.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>{{ __('messages.nav.documents') }}</span>
            </a>
            @endif
        </div>
    </nav>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-3 py-2 text-sm text-left text-gray-600 dark:text-gray-400 hover:bg-gray-100 rounded-lg">Sign Out</button>
        </form>
    </div>
</div>