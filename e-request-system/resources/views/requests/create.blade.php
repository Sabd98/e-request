{{-- resources/views/requests/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create New Request</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Request*</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $request->title ?? '') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi*</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3" required>{{ old('description', $request->description ?? '') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="request_type" class="form-label">Tipe Request*</label>
                            <select class="form-select @error('request_type') is-invalid @enderror"
                                id="request_type" name="request_type" required>
                                <option value="">Pilih Tipe Request</option>
                                <option value="cuti" @selected(old('request_type', $request->request_type ?? '') == 'cuti')>Permintaan Cuti</option>
                                <option value="atk" @selected(old('request_type', $request->request_type ?? '') == 'atk')>Pengadaan ATK</option>
                                <option value="akses" @selected(old('request_type', $request->request_type ?? '') == 'akses')>Permintaan Akses Sistem</option>
                                <option value="reimbursement" @selected(old('request_type', $request->request_type ?? '') == 'reimbursement')>Reimbursement</option>
                            </select>
                            @error('request_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Lampiran</label>
                            <input class="form-control @error('attachment') is-invalid @enderror"
                                type="file" id="attachment" name="attachment">
                            @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if(isset($request) && $request->attachment)
                            <div class="mt-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-paperclip me-2"></i>
                                    <span class="text-truncate" style="max-width: 250px">
                                        {{ $request->attachment }}
                                    </span>
                                    <a href="{{ route('requests.download', $request->id) }}"
                                        class="btn btn-sm btn-outline-success ms-2">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox"
                                        id="remove_attachment" name="remove_attachment">
                                    <label class="form-check-label text-danger" for="remove_attachment">
                                        <i class="fas fa-trash-alt"></i> Hapus lampiran
                                    </label>
                                </div>
                            </div>
                            @endif
                            <small class="text-muted">Format: PDF, JPG, PNG (Maks. 5MB)</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection