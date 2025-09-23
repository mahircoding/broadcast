@extends('layouts.app')

@section('title', 'Riwayat Broadcast')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-history me-2"></i>
        Riwayat Broadcast
    </h1>
    <a href="{{ route('broadcast.index') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>
        Broadcast Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($broadcasts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pesan</th>
                            <th>Penerima</th>
                            <th>Status</th>
                            <th>Hasil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($broadcasts as $broadcast)
                            <tr>
                                <td>
                                    <strong>{{ $broadcast->created_at->format('d/m/Y') }}</strong>
                                    <br><small class="text-muted">{{ $broadcast->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <p class="mb-1">{{ Str::limit($broadcast->message, 80) }}</p>
                                    @if(strlen($broadcast->message) > 80)
                                        <small class="text-muted">...</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ count($broadcast->recipients ?? []) }} kontak
                                    </span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($broadcast->status === 'completed') bg-success
                                        @elseif($broadcast->status === 'failed') bg-danger
                                        @elseif($broadcast->status === 'processing') bg-warning text-dark
                                        @else bg-secondary
                                        @endif
                                    ">
                                        @if($broadcast->status === 'completed') Selesai
                                        @elseif($broadcast->status === 'failed') Gagal
                                        @elseif($broadcast->status === 'processing') Memproses
                                        @else Pending
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($broadcast->status === 'completed')
                                        <div class="small">
                                            <div class="text-success">
                                                <i class="fas fa-check me-1"></i>
                                                {{ $broadcast->total_success }} berhasil
                                            </div>
                                            @if($broadcast->total_failed > 0)
                                                <div class="text-danger">
                                                    <i class="fas fa-times me-1"></i>
                                                    {{ $broadcast->total_failed }} gagal
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($broadcast->status === 'failed')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Error saat mengirim
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('broadcast.show', $broadcast) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Menampilkan {{ $broadcasts->firstItem() }} - {{ $broadcasts->lastItem() }}
                        dari {{ $broadcasts->total() }} broadcast
                    </small>
                </div>
                <div>
                    {{ $broadcasts->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada riwayat broadcast</h5>
                <p class="text-muted">
                    Mulai dengan <a href="{{ route('broadcast.index') }}">mengirim broadcast pertama</a>
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Statistics -->
@if($broadcasts->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h4>{{ $broadcasts->total() }}</h4>
                <p class="mb-0">Total Broadcast</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h4>{{ $broadcasts->where('status', 'completed')->count() }}</h4>
                <p class="mb-0">Berhasil</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h4>{{ $broadcasts->where('status', 'processing')->count() }}</h4>
                <p class="mb-0">Sedang Proses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h4>{{ $broadcasts->where('status', 'failed')->count() }}</h4>
                <p class="mb-0">Gagal</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
