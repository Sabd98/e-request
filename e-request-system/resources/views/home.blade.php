@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                @if(auth()->user()->role === 'admin')
                Admin Dashboard
                @elseif(auth()->user()->role === 'approver')
                Approver Dashboard
                @else
                Requestor Dashboard
                @endif
            </h1>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="row mb-4">
        @if(auth()->user()->role === 'admin')
        <!-- Admin Stats -->
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4">{{ App\Models\User::count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Requests</h5>
                    <p class="card-text display-4">{{ App\Models\Request::whereIn('status', ['submitted', 'approved'])->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Approval</h5>
                    <p class="card-text display-4">{{ App\Models\Request::where('status', 'submitted')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Trash Items</h5>
                    <p class="card-text display-4">{{ App\Models\Request::onlyTrashed()->count() }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Main Content Based on Role -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        @if(auth()->user()->role === 'admin')
                        System Overview
                        @elseif(auth()->user()->role === 'approver')
                        Pending Approvals
                        @else
                        Your Recent Requests
                        @endif
                    </h3>
                </div>

                <div class="card-body">
                    @if(auth()->user()->role === 'admin')
                    <!-- Admin Dashboard Content -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
          
                                        <a href="{{ route('requests.trash') }}" class="btn btn-outline-warning btn-lg mb-2">
                                            <i class="fas fa-trash-restore me-2"></i> View Trash
                                        </a>
                                        <a href="{{ route('requests.create') }}" class="btn btn-outline-success btn-lg">
                                            <i class="fas fa-plus-circle me-2"></i> Create New Request
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Recent System Activity</h5>
                                </div>
                                <div class="card-body">
                                    @if($recentRequests->isEmpty())
                                    <p class="text-muted">No recent activity</p>
                                    @else
                                    <div class="list-group">
                                        @foreach($recentRequests as $request)
                                        <a href="{{ route('requests.show', $request->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $request->title }}</h6>
                                                <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1">{{ Str::limit($request->description, 80) }}</p>
                                            <small>
                                                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'info') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </small>
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @elseif(auth()->user()->role === 'approver')
                    <!-- Approver Dashboard Content -->
                    @if($pendingApprovals->isEmpty())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        All requests have been processed! No pending approvals.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Requestor</th>
                                    <th>Type</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingApprovals as $request)
                                <tr>
                                    <td>{{ $request->title }}</td>
                                    <td>{{ $request->creator->name }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($request->request_type) }}</span></td>
                                    <td>{{ $request->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('approvals.show', $request->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @else
                    <!-- Requestor Dashboard Content -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <a href="{{ route('requests.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Create New Request
                            </a>
                        </div>
                        <div>
                            <span class="me-2">Filter:</span>
                            <div class="btn-group">
                                <a href="?status=all" class="btn btn-outline-secondary {{ request('status') === 'all' || !request('status') ? 'active' : '' }}">All</a>
                                <a href="?status=draft" class="btn btn-outline-secondary {{ request('status') === 'draft' ? 'active' : '' }}">Drafts</a>
                                <a href="?status=submitted" class="btn btn-outline-info {{ request('status') === 'submitted' ? 'active' : '' }}">Submitted</a>
                                <a href="?status=approved" class="btn btn-outline-success {{ request('status') === 'approved' ? 'active' : '' }}">Approved</a>
                                <a href="?status=rejected" class="btn btn-outline-danger {{ request('status') === 'rejected' ? 'active' : '' }}">Rejected</a>
                            </div>
                        </div>
                    </div>

                    @if($userRequests->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You haven't created any requests yet.
                    </div>
                    @else
                    <!-- PERBAIKAN: Gunakan hanya satu jenis tampilan (tabel ATAU list group) -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userRequests as $request)
                                <tr>
                                    <td>{{ $request->title }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst($request->request_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                $request->status === 'draft' ? 'secondary' : 
                                ($request->status === 'submitted' ? 'info' : 
                                ($request->status === 'approved' ? 'success' : 'danger')) 
                            }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($request->status === 'draft')
                                        <a href="{{ route('requests.edit', $request->id) }}" class="btn btn-sm btn-outline-warning ms-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $userRequests->links() }}
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
