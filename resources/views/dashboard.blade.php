@extends('layouts.app')

@section('title', 'Dashboard - WhatsApp Broadcast')

@section('content')
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card user-welcome-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-user-circle me-2 text-success"></i>
                            Selamat datang, {{ Auth::user()->name }}!
                        </h4>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-envelope me-1"></i>
                            {{ Auth::user()->email }}
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Login terakhir: {{ Auth::user()->updated_at->format('d M Y, H:i') }}
                        </small>
                    </div>
                    <div class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('settings.waboxapp') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-cog me-1"></i>
                                Pengaturan
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm btn-logout-confirm">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard
        </h1>
    </div>
</div>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalContacts ?? 0 }}</h4>
                        <p class="card-text">Total Kontak</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $activeContacts ?? 0 }}</h4>
                        <p class="card-text">Kontak Aktif</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalGroups ?? 0 }}</h4>
                        <p class="card-text">Total Grup</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalBroadcasts ?? 0 }}</h4>
                        <p class="card-text">Total Broadcast</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bullhorn fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-rocket me-2"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Kontak Baru
                    </a>
                    <a href="{{ route('contacts.upload') }}" class="btn btn-info">
                        <i class="fas fa-upload me-2"></i>
                        Upload Kontak CSV
                    </a>
                    <a href="{{ route('broadcast.index') }}" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>
                        Kirim Broadcast
                    </a>
                    <a href="{{ route('settings.waboxapp') }}" class="btn btn-warning">
                        <i class="fas fa-cogs me-2"></i>
                        Pengaturan WaboxApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Broadcasts -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Broadcast Terbaru
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recentBroadcasts) && $recentBroadcasts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentBroadcasts as $broadcast)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">
                                            {{ Str::limit($broadcast->message, 50) }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $broadcast->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <span class="badge
                                        @if($broadcast->status === 'completed') bg-success
                                        @elseif($broadcast->status === 'failed') bg-danger
                                        @elseif($broadcast->status === 'processing') bg-warning
                                        @else bg-secondary
                                        @endif
                                    ">
                                        {{ ucfirst($broadcast->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('broadcast.history') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua Riwayat
                        </a>
                    </div>
                @else
                    <p class="text-muted mb-0">Belum ada broadcast yang dikirim.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- API Status Check -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plug me-2"></i>
                    Status Koneksi API
                </h5>
            </div>
            <div class="card-body">
                <button id="testApiBtn" class="btn btn-outline-primary">
                    <i class="fas fa-wifi me-2"></i>
                    Test Koneksi WaboxApp
                </button>
                <div id="apiStatus" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#testApiBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Testing...');

        $.ajax({
            url: '{{ route("broadcast.test-connection") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#apiStatus').html(`
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        ${response.message}
                    </div>
                `);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                $('#apiStatus').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${response.message || 'Terjadi kesalahan saat testing koneksi'}
                    </div>
                `);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
