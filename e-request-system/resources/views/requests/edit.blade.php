@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Request: {{ $request->title }}</h5>
                    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Requests
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('requests.update', $request->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Request Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $request->title) }}"
                                required autofocus>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="4" required>{{ old('description', $request->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="request_type" class="form-label">Request Type *</label>
                            <select class="form-select @error('request_type') is-invalid @enderror"
                                id="request_type" name="request_type" required>
                                <option value="">Select Request Type</option>
                                <option value="cuti" @selected(old('request_type', $request->request_type) == 'cuti')>Leave Request</option>
                                <option value="atk" @selected(old('request_type', $request->request_type) == 'atk')>Office Supplies Request</option>
                                <option value="akses" @selected(old('request_type', $request->request_type) == 'akses')>System Access Request</option>
                                <option value="reimbursement" @selected(old('request_type', $request->request_type) == 'reimbursement')>Reimbursement</option>
                            </select>
                            @error('request_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="attachment" class="form-label">Attachment</label>
                            <input class="form-control @error('attachment') is-invalid @enderror"
                                type="file" id="attachment" name="attachment">
                            @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($request->attachment)
                            <div class="mt-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-paperclip me-2"></i>
                                    <span class="text-truncate" style="max-width: 250px">
                                        {{ basename($request->attachment) }}
                                    </span>
                                    <a href="{{ route('requests.download', $request->id) }}"
                                        class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox"
                                        id="remove_attachment" name="remove_attachment">
                                    <label class="form-check-label text-danger" for="remove_attachment">
                                        <i class="fas fa-trash-alt me-1"></i> Remove current attachment
                                    </label>
                                </div>
                            </div>
                            @endif
                            <small class="text-muted">Format: PDF, JPG, PNG (Max 5MB)</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Update Request
                            </button>
                        </div>
                    </form>

                    @if ($request->status === 'draft')
                    <div class="mt-3">
                        <form method="POST" action="{{ route('requests.submit', $request->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection