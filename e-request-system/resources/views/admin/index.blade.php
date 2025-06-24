@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Admin Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">Admin Dashboard</h1>
                    <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6">
                        <i class="fas fa-shield-alt me-1"></i> Administrator
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-5">{{ $stats['total_users'] }}</p>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Active Requests</h5>
                            <p class="card-text display-5">{{ $stats['active_requests'] }}</p>
                        </div>
                        <i class="fas fa-list fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Pending Approval</h5>
                            <p class="card-text display-5">{{ $stats['pending_requests'] }}</p>
                        </div>
                        <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Trash Items</h5>
                            <p class="card-text display-5">{{ $stats['trashed_requests'] }}</p>
                        </div>
                        <i class="fas fa-trash-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i> User Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage all system users, roles, and permissions.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Active Users</span>
                            <span class="badge bg-primary rounded-pill">{{ $stats['active_users'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Approvers</span>
                            <span class="badge bg-info rounded-pill">{{ $stats['approvers'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Requestors</span>
                            <span class="badge bg-success rounded-pill">{{ $stats['requestors'] }}</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Request Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage all requests and approval processes.</p>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                        
                             aria-valuenow="{{ $stats['approved_percentage'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                             Approved: {{ $stats['approved_requests'] }}
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" 
                            
                             aria-valuenow="{{ $stats['rejected_percentage'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                             Rejected: {{ $stats['rejected_requests'] }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Draft: {{ $stats['draft_requests'] }}</span>
                        <span>Pending: {{ $stats['pending_requests'] }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('requests.index') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-external-link-alt me-2"></i> View All Requests
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-trash-restore me-2"></i> Trash System</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage deleted requests and restore if necessary.</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Requests in trash will be permanently deleted after 30 days.
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Recently deleted:</span>
                        <span>{{ $stats['recently_deleted'] }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('requests.trash') }}" class="btn btn-outline-warning w-100">
                        <i class="fas fa-trash-restore me-2"></i> Manage Trash
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Recent System Activity</h5>
                </div>
                <div class="card-body">
                    @if($recentActivity->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activity found</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>User</th>
                                        <th>Request</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivity as $activity)
                                    <tr>
                                        <td>
                                            @if($activity->action === 'created')
                                                <span class="badge bg-success">Created</span>
                                            @elseif($activity->action === 'updated')
                                                <span class="badge bg-primary">Updated</span>
                                            @elseif($activity->action === 'deleted')
                                                <span class="badge bg-danger">Deleted</span>
                                            @elseif($activity->action === 'approved')
                                                <span class="badge bg-info">Approved</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($activity->action) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $activity->user->name }}</td>
                                        <td>
                                            <a href="{{ route('requests.show', $activity->request_id) }}">
                                                {{ Str::limit($activity->request->title, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-uppercase">
                                                {{ $activity->request->request_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $activity->request->status === 'approved' ? 'success' : 
                                                ($activity->request->status === 'rejected' ? 'danger' : 
                                                ($activity->request->status === 'submitted' ? 'info' : 'secondary')) 
                                            }}">
                                                {{ ucfirst($activity->request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $recentActivity->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection