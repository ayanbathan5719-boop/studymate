<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }
        
        // Filter by module
        if ($request->has('module') && $request->module !== 'all') {
            $query->where('module', $request->module);
        }
        
        // Date range filter
        if ($request->has('from') && $request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        
        if ($request->has('to') && $request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        $logs = $query->paginate(20);
        
        // Get filter options
        $users = User::orderBy('name')->get();
        $actions = AuditLog::distinct()->pluck('action');
        $modules = AuditLog::distinct()->pluck('module');
        
        return view('admin.audit.index', [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'modules' => $modules,
            'filters' => $request->only(['user_id', 'action', 'module', 'from', 'to'])
        ]);
    }

    /**
     * Display a specific audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        
        return view('admin.audit.show', [
            'log' => $auditLog
        ]);
    }

    /**
     * Export audit logs (CSV).
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user');
        
        // Apply same filters as index
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }
        
        if ($request->has('module') && $request->module !== 'all') {
            $query->where('module', $request->module);
        }
        
        if ($request->has('from') && $request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        
        if ($request->has('to') && $request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        $logs = $query->latest()->get();
        
        // Generate CSV
        $filename = 'audit-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add headers
        fputcsv($handle, ['ID', 'User', 'Action', 'Module', 'Description', 'IP Address', 'Timestamp']);
        
        // Add data
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user ? $log->user->name : 'System',
                $log->action,
                $log->module,
                $log->description,
                $log->ip_address,
                $log->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}