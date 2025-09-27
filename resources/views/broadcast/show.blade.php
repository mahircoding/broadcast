@extends('layouts.app')

@section('title', 'Detail Broadcast')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-bullhorn me-2"></i>
        Detail Broadcast
    </h1>
    <a href="{{ route('broadcast.history') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        Kembali ke Riwayat
    </a>
</div>

<div class="row">
    <!-- Broadcast Info -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Broadcast
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tanggal Kirim:</strong> {{ $broadcastLog->created_at->format('d F Y H:i:s') }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge
                                @if($broadcastLog->status === 'completed') bg-success
                                @elseif($broadcastLog->status === 'failed') bg-danger
                                @elseif($broadcastLog->status === 'processing') bg-warning text-dark
                                @else bg-secondary
                                @endif
                            ">
                                @if($broadcastLog->status === 'completed') Selesai
                                @elseif($broadcastLog->status === 'failed') Gagal
                                @elseif($broadcastLog->status === 'processing') Memproses
                                @else Pending
                                @endif
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Penerima:</strong> {{ $broadcastLog->total_sent }}</p>
                        <p><strong>Berhasil Terkirim:</strong>
                            <span class="text-success">{{ $broadcastLog->total_success }}</span>
                        </p>
                        @if($broadcastLog->total_failed > 0)
                        <p><strong>Gagal Terkirim:</strong>
                            <span class="text-danger">{{ $broadcastLog->total_failed }}</span>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Content -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comment me-2"></i>
                    Isi Pesan
                </h5>
            </div>
            <div class="card-body">
                @if($broadcastLog->hasImage())
                    <div class="mb-3">
                        <strong>Gambar:</strong>
                        <div class="mt-2">
                            <img src="{{ $broadcastLog->getImageUrl() }}" 
                                 alt="Broadcast Image" 
                                 class="img-fluid rounded border" 
                                 style="max-width: 300px; max-height: 200px; object-fit: cover;">
                        </div>
                    </div>
                @endif
                <div class="border rounded p-3 bg-light">
                    <strong>Pesan:</strong><br>
                    {!! nl2br(e($broadcastLog->message)) !!}
                </div>
            </div>
        </div>

        <!-- Recipients List -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Daftar Penerima
                </h5>
            </div>
            <div class="card-body">
                @if($contacts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Nomor HP</th>
                                    <th>Grup</th>
                                    <th>Status Pengiriman</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contacts as $contact)
                                    @php
                                        $deliveryStatus = collect($broadcastLog->response_data ?? [])->firstWhere('phone', $contact->formatted_phone);
                                    @endphp
                                    <tr>
                                        <td>{{ $contact->name }}</td>
                                        <td><span class="font-monospace">{{ $contact->phone_number }}</span></td>
                                        <td>
                                            @if($contact->group)
                                                <span class="badge bg-secondary">{{ $contact->group }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($deliveryStatus)
                                                @if($deliveryStatus['success'])
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Terkirim
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Gagal
                                                    </span>
                                                    @if(isset($deliveryStatus['error']))
                                                        <br><small class="text-danger">{{ $deliveryStatus['error'] }}</small>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Tidak ada data penerima yang tersedia.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistik Pengiriman
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Dikirim:</span>
                        <strong>{{ $broadcastLog->total_sent }}</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between text-success">
                        <span>Berhasil:</span>
                        <strong>{{ $broadcastLog->total_success }}</strong>
                    </div>
                    @if($broadcastLog->total_sent > 0)
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success"
                             style="width: {{ ($broadcastLog->total_success / $broadcastLog->total_sent) * 100 }}%"></div>
                    </div>
                    @endif
                </div>

                @if($broadcastLog->total_failed > 0)
                <div class="mb-3">
                    <div class="d-flex justify-content-between text-danger">
                        <span>Gagal:</span>
                        <strong>{{ $broadcastLog->total_failed }}</strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger"
                             style="width: {{ ($broadcastLog->total_failed / $broadcastLog->total_sent) * 100 }}%"></div>
                    </div>
                </div>
                @endif

                @if($broadcastLog->total_sent > 0)
                <div class="text-center mt-3">
                    <div class="h4 text-success">
                        {{ round(($broadcastLog->total_success / $broadcastLog->total_sent) * 100, 1) }}%
                    </div>
                    <small class="text-muted">Tingkat Keberhasilan</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tools me-2"></i>
                    Aksi
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($broadcastLog->status === 'completed' && $broadcastLog->total_failed > 0)
                        <button class="btn btn-warning" onclick="resendFailed()">
                            <i class="fas fa-redo me-1"></i>
                            Kirim Ulang yang Gagal
                        </button>
                    @endif

                    <button class="btn btn-primary" onclick="duplicateBroadcast()">
                        <i class="fas fa-copy me-1"></i>
                        Duplikasi Broadcast
                    </button>

                    <a href="{{ route('broadcast.index') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>
                        Broadcast Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Details -->
        @if($broadcastLog->status === 'failed' && isset($broadcastLog->response_data['error']))
        <div class="card mt-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Detail Error
                </h6>
            </div>
            <div class="card-body">
                <p class="text-danger mb-0">
                    {{ $broadcastLog->response_data['error'] }}
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function resendFailed() {
    if (confirm('Kirim ulang pesan ke kontak yang gagal menerima?')) {
        // Implement resend failed logic
        alert('Fitur ini akan segera tersedia');
    }
}

function duplicateBroadcast() {
    // Redirect to broadcast form with pre-filled message
    const message = @json($broadcastLog->message);
    const url = new URL('{{ route("broadcast.index") }}');
    url.searchParams.set('message', message);
    window.location.href = url.toString();
}
</script>
@endpush
