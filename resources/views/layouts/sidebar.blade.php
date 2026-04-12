<div class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
    @php use Illuminate\Support\Facades\Auth; @endphp
    <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
                <span class="text-white font-bold">L</span>
            </div>
            <span class="text-lg font-semibold text-gray-900 dark:text-white">LegalFirm</span>
        </a>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-4">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Main</p>
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Dashboard</span>
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Management</p>
            <a href="{{ route('cases.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('cases.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Cases</span>
            </a>
            <a href="{{ route('tasks.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Tasks</span>
            </a>
            <a href="{{ route('documents.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('documents.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Documents</span>
            </a>
            <a href="{{ route('billing.invoices') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('billing.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Billing</span>
            </a>
            <a href="{{ route('trust-accounts.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('trust-accounts.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Trust Accounts</span>
            </a>
            <a href="{{ route('calendar.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('calendar.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Calendar</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100' }}">
                <span>Reports</span>
            </a>
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
        </div>
    </nav>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-3 py-2 text-sm text-left text-gray-600 dark:text-gray-400 hover:bg-gray-100 rounded-lg">Sign Out</button>
        </form>
    </div>
</div>