@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Request Menunggu Persetujuan</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($requests->isEmpty())
    <div class="alert alert-info">
        Tidak ada request yang menunggu persetujuan
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
                        <th>Tanggal Diajukan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->title }}</td>
                        <td>{{ $request->creator->name }}</td>
                        <td>{{ ucfirst($request->request_type) }}</td>
                        <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('approvals.show', $request->id) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-check-circle"></i> Proses
                            </a>
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