@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Trash - Request yang Dihapus</h2>
        <a href="{{ route('requests.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Kembali ke Request
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($requests->isEmpty())
    <div class="alert alert-info">
        Tidak ada request di trash
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Dibuat Oleh</th>
                        <th>Tipe</th>
                        <th>Status Terakhir</th>
                        <th>Tanggal Dihapus</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->title }}</td>
                        <td>{{ $request->creator->name }}</td>
                        <td>{{ ucfirst($request->request_type) }}</td>
                        <td>
                            <span class="badge 
                                    @if($request->status === 'draft') bg-secondary
                                    @elseif($request->status === 'submitted') bg-info
                                    @elseif($request->status === 'approved') bg-success
                                    @else bg-danger
                                    @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->deleted_at->format('d M Y H:i') }}</td>
                        <td>
                            <form action="{{ route('requests.restore', $request->id) }}"
                                method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-trash-restore"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('requests.force-delete', $request->id) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus permanen request ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i> Hapus Permanen
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection