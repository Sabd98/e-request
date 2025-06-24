@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Persetujuan Request: {{ $request->title }}</h2>
        <a href="{{ route('approvals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Tampilkan detail request -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Dibuat Oleh</dt>
                        <dd class="col-sm-8">{{ $request->creator->name }}</dd>

                        <dt class="col-sm-4">Tanggal Dibuat</dt>
                        <dd class="col-sm-8">{{ $request->created_at->format('d M Y H:i') }}</dd>

                        <dt class="col-sm-4">Tipe Request</dt>
                        <dd class="col-sm-8">{{ ucfirst($request->request_type) }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    @if($request->attachment)
                    <a href="{{ Storage::disk('attachments')->url($request->attachment) }}"
                        target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-download"></i> Download Lampiran
                    </a>
                    @endif
                </div>
            </div>

            <hr>

            <h5>Deskripsi</h5>
            <p>{{ $request->description }}</p>
        </div>
    </div>

    <!-- Form Approval -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Persetujuan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Form Approve -->
                <div class="col-md-6">
                    <form action="{{ route('approve', $request->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="approveNotes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="approveNotes" name="notes"
                                rows="3" placeholder="Tambahkan catatan jika perlu"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check"></i> Setujui Request
                        </button>
                    </form>
                </div>

                <!-- Form Reject -->
                <div class="col-md-6">
                    <form action="{{ route('reject', $request->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rejectNotes" class="form-label">Catatan Penolakan*</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="rejectNotes" name="notes" rows="3"
                                placeholder="Alasan penolakan" required></textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            <i class="fas fa-times"></i> Tolak Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection