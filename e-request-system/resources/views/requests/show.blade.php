@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Request: {{ $request->title }}</h2>
        <a href="{{ route('requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Request</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Dibuat Oleh</dt>
                        <dd class="col-sm-8">{{ $request->creator->name }}</dd>

                        <dt class="col-sm-4">Tanggal Dibuat</dt>
                        <dd class="col-sm-8">{{ $request->created_at->format('d M Y H:i') }}</dd>

                        <dt class="col-sm-4">Tipe Request</dt>
                        <dd class="col-sm-8">{{ ucfirst($request->request_type) }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge 
                                @if($request->status === 'draft') bg-secondary
                                @elseif($request->status === 'submitted') bg-info
                                @elseif($request->status === 'approved') bg-success
                                @else bg-danger
                                @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <h5>Lampiran</h5>
                    @if($request->attachment)
                    <a href="{{ Storage::disk('attachments')->url($request->attachment) }}"
                        target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-download"></i> Download Lampiran
                    </a>
                    @else
                    <p class="text-muted">Tidak ada lampiran</p>
                    @endif
                </div>
            </div>

            <hr>

            <h5>Deskripsi</h5>
            <p>{{ $request->description }}</p>
        </div>
    </div>

    <!-- Approval Log Section -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Riwayat Approval</h5>
        </div>
        <div class="card-body">
            @if($request->approvalLogs->isEmpty())
            <p class="text-muted">Belum ada riwayat approval</p>
            @else
            <ul class="list-group">
                @foreach($request->approvalLogs as $log)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $log->user->name }}</strong>
                            <span class="badge 
                                    @if($log->action === 'approve') bg-success
                                    @else bg-danger
                                    @endif">
                                {{ ucfirst($log->action) }}
                            </span>
                        </div>
                        <small>{{ $log->created_at->format('d M Y H:i') }}</small>
                    </div>
                    @if($log->notes)
                    <div class="mt-2">
                        <strong>Catatan:</strong> {{ $log->notes }}
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection