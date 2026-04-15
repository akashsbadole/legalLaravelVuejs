<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LegalCase;
use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        
        $totalCases = LegalCase::count();
        $activeCases = LegalCase::whereIn('status_id', [1, 2])->count();
        
        $totalClients = User::where('role', 'client')->count();
        $newClientsThisMonth = User::where('role', 'client')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $revenueThisMonth = Invoice::where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_amount');

        $recentActivity = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'user' => $log->user?->name ?? 'System',
                    'icon' => $this->getActivityIcon($log->action),
                    'iconColor' => $this->getActivityColor($log->action),
                    'time' => $log->created_at->diffForHumans()
                ];
            });

        $casesByStatus = LegalCase::select('status_id', DB::raw('count(*) as count'))
            ->with('status')
            ->groupBy('status_id')
            ->get()
            ->map(fn($c) => [
                'status' => $c->status?->name ?? 'Unknown',
                'count' => $c->count
            ]);

        return response()->json([
            'stats' => [
                'users' => $totalUsers,
                'newUsersThisMonth' => $newUsersThisMonth,
                'cases' => $totalCases,
                'activeCases' => $activeCases,
                'clients' => $totalClients,
                'newClientsThisMonth' => $newClientsThisMonth,
                'revenue' => $totalRevenue,
                'revenueThisMonth' => $revenueThisMonth
            ],
            'recent_activity' => $recentActivity,
            'cases_by_status' => $casesByStatus
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'users' => $users->items(),
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage()
        ]);
    }

    public function toggleUserStatus(User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'User status updated',
            'user' => [
                'id' => $user->id,
                'is_active' => $user->is_active
            ]
        ]);
    }

    public function auditLogs(Request $request): JsonResponse
    {
        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'logs' => $logs->items(),
            'total' => $logs->total(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage()
        ]);
    }

    private function getActivityIcon(string $action): string
    {
        $icons = [
            'create' => 'M12 4v16m8-8H4',
            'update' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'delete' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            'login' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
        ];
        return $icons[$action] ?? $icons['update'];
    }

    private function getActivityColor(string $action): string
    {
        $colors = [
            'create' => 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
            'update' => 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
            'delete' => 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300',
            'login' => 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
        ];
        return $colors[$action] ?? $colors['update'];
    }
}