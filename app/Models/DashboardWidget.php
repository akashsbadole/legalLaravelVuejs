<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidget extends Model
{
    protected $table = 'dashboard_widgets';

    protected $fillable = [
        'user_id', 'widget_type', 'title', 'position',
        'is_visible', 'config_json', 'size',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'config_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getWidgetTypes(): array
    {
        return [
            'stats_overview' => 'Stats Overview',
            'recent_cases' => 'Recent Cases',
            'pending_tasks' => 'Pending Tasks',
            'upcoming_deadlines' => 'Upcoming Deadlines',
            'recent_documents' => 'Recent Documents',
            'unpaid_invoices' => 'Unpaid Invoices',
            'case_status_chart' => 'Case Status Chart',
            'revenue_chart' => 'Revenue Chart',
            'time_tracking' => 'Time Tracking Summary',
            'team_activity' => 'Team Activity',
            'calendar_widget' => 'Calendar Widget',
            'quick_actions' => 'Quick Actions',
        ];
    }

    public static function getDefaultWidgets(): array
    {
        return [
            ['widget_type' => 'stats_overview', 'title' => 'Overview', 'position' => 1, 'size' => 'full'],
            ['widget_type' => 'recent_cases', 'title' => 'Recent Cases', 'position' => 2, 'size' => 'half'],
            ['widget_type' => 'pending_tasks', 'title' => 'Pending Tasks', 'position' => 3, 'size' => 'half'],
            ['widget_type' => 'unpaid_invoices', 'title' => 'Unpaid Invoices', 'position' => 4, 'size' => 'half'],
            ['widget_type' => 'revenue_chart', 'title' => 'Revenue', 'position' => 5, 'size' => 'full'],
        ];
    }

    public static function getSizes(): array
    {
        return [
            'small' => 'Small (25%)',
            'half' => 'Half (50%)',
            'full' => 'Full (100%)',
        ];
    }
}
