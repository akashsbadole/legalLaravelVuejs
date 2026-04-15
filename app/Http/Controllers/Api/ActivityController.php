<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LegalCase;
use App\Models\CaseTask;
use App\Models\Invoice;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 20);
        
        $activities = [];
        
        if ($type === 'all' || $type === 'cases') {
            $cases = LegalCase::latest()->limit($limit)->get()->map(fn($c) => [
                'id' => 'case-' . $c->id,
                'type' => 'cases',
                'description' => 'Case "' . $c->title . '" was ' . ($c->wasRecentlyCreated ? 'created' : 'updated'),
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'iconColor' => 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
                'created_at' => $c->created_at
            ]);
            $activities = array_merge($activities, $cases->toArray());
        }
        
        if ($type === 'all' || $type === 'tasks') {
            $tasks = CaseTask::latest()->limit($limit)->get()->map(fn($t) => [
                'id' => 'task-' . $t->id,
                'type' => 'tasks',
                'description' => 'Task "' . $t->title . '" was ' . ($t->status === 'completed' ? 'completed' : 'updated'),
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'iconColor' => 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
                'created_at' => $t->created_at
            ]);
            $activities = array_merge($activities, $tasks->toArray());
        }
        
        if ($type === 'all' || $type === 'invoices') {
            $invoices = Invoice::latest()->limit($limit)->get()->map(fn($i) => [
                'id' => 'invoice-' . $i->id,
                'type' => 'invoices',
                'description' => 'Invoice ' . $i->invoice_number . ' was created - ₹' . number_format($i->total_amount, 2),
                'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
                'iconColor' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
                'created_at' => $i->created_at
            ]);
            $activities = array_merge($activities, $invoices->toArray());
        }
        
        if ($type === 'all' || $type === 'documents') {
            $documents = Document::latest()->limit($limit)->get()->map(fn($d) => [
                'id' => 'doc-' . $d->id,
                'type' => 'documents',
                'description' => 'Document "' . $d->name . '" was uploaded',
                'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                'iconColor' => 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
                'created_at' => $d->created_at
            ]);
            $activities = array_merge($activities, $documents->toArray());
        }
        
        usort($activities, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        
        return response()->json([
            'activities' => array_slice($activities, 0, $limit),
            'total' => count($activities)
        ]);
    }
}