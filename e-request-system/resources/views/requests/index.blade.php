@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Request</h2>
    <a href="{{ route('requests.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Buat Request Baru
    </a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->title }}</td>
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
                        <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('requests.show', $request->id) }}"
                                class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($request->isEditable())
                            <a href="{{ route('requests.edit', $request->id) }}"
                                class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('requests.submit', $request->id) }}"
                                method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </button>
                            </form>

                            <form action="{{ route('requests.destroy', $request->id) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection