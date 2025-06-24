<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Request as RequestModel;
use App\Models\ApprovalLog;

class AdminController extends Controller
{
    public function index()
    {
        // Hanya admin yang bisa akses
        $this->authorize('admin-access');

        // Statistik untuk dashboard
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'approvers' => User::where('role', 'approver')->count(),
            'requestors' => User::where('role', 'requestor')->count(),
            'active_requests' => RequestModel::count(),
            'pending_requests' => RequestModel::where('status', 'submitted')->count(),
            'approved_requests' => RequestModel::where('status', 'approved')->count(),
            'rejected_requests' => RequestModel::where('status', 'rejected')->count(),
            'draft_requests' => RequestModel::where('status', 'draft')->count(),
            'trashed_requests' => RequestModel::onlyTrashed()->count(),
            'recently_deleted' => RequestModel::onlyTrashed()
                ->where('deleted_at', '>', now()->subDays(7))
                ->count(),
        ];

        // Hitung persentase
        $totalProcessed = $stats['approved_requests'] + $stats['rejected_requests'];
        $stats['approved_percentage'] = $totalProcessed > 0
            ? round(($stats['approved_requests'] / $totalProcessed) * 100, 1)
            : 0;

        $stats['rejected_percentage'] = $totalProcessed > 0
            ? round(($stats['rejected_requests'] / $totalProcessed) * 100, 1)
            : 0;

        // Aktivitas terbaru dengan pagination
        $recentActivity = ApprovalLog::with(['user', 'request'])
            ->latest()
            ->paginate(10);

        return view('admin.index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity
        ]);
    }
}
