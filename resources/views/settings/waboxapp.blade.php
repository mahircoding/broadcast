@extends('layouts.app')

@section('title', 'Pengaturan WaboxApp')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fab fa-whatsapp me-2 text-success"></i>
                Pengaturan WaboxApp
            </h1>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle me-2"></i>
                Cara Mendapatkan Token dan UID
            </h6>
            <ol class="mb-2">
                <li>Buka <a href="https://waboxapp.com" target="_blank">waboxapp.com</a> dan login</li>
                <li>Buka menu "API" di dashboard</li>
                <li>Copy nilai <strong>Token</strong> dan <strong>UID</strong></li>
                <li>Paste di form di bawah ini</li>
                <li>Atur <strong>Delay Broadcast</strong> untuk menghindari banned WhatsApp</li>
            </ol>
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Sistem dilengkapi fitur anti-ban dengan delay otomatis dan batch processing.
            </small>
        </div>

        <!-- Anti-Ban Features Alert -->
        <div class="alert alert-success">
            <h6 class="alert-heading">
                <i class="fas fa-shield-alt me-2"></i>
                Fitur Perlindungan Anti-Ban
            </h6>
            <ul class="mb-2">
                <li><strong>Delay Antar Pesan:</strong> Jeda waktu yang dapat dikonfigurasi antara setiap pengiriman</li>
                <li><strong>Batch Processing:</strong> Sistem akan pause lebih lama setiap menyelesaikan batch tertentu</li>
                <li><strong>Unique Message ID:</strong> Setiap pesan memiliki ID unik untuk tracking</li>
                <li><strong>Rate Limiting:</strong> Mencegah pengiriman terlalu cepat yang dapat memicu ban</li>
            </ul>
            <small class="text-success">
                <i class="fas fa-check-circle me-1"></i>
                Disarankan menggunakan delay 3-5 detik untuk keamanan optimal.
            </small>
        </div>

        <!-- Settings Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Konfigurasi API WaboxApp
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.waboxapp.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="wabox_api_url" class="form-label">API URL</label>
                        <input type="url"
                               class="form-control @error('wabox_api_url') is-invalid @enderror"
                               id="wabox_api_url"
                               name="wabox_api_url"
                               value="{{ old('wabox_api_url', $settings['wabox_api_url']) }}"
                               required>
                        @error('wabox_api_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Default: https://www.waboxapp.com/api
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="wabox_token" class="form-label">
                            API Token <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control @error('wabox_token') is-invalid @enderror"
                                   id="wabox_token"
                                   name="wabox_token"
                                   value="{{ old('wabox_token', $settings['wabox_token']) }}"
                                   placeholder="Masukkan API Token dari WaboxApp"
                                   required>
                        </div>
                        @error('wabox_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Token API dari dashboard WaboxApp.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="wabox_uid" class="form-label">
                            UID (User ID) <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('wabox_uid') is-invalid @enderror"
                               id="wabox_uid"
                               name="wabox_uid"
                               value="{{ old('wabox_uid', $settings['wabox_uid']) }}"
                               placeholder="Masukkan UID dari WaboxApp"
                               required>
                        @error('wabox_uid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            User ID dari dashboard WaboxApp
                        </div>
                    </div>

                    <!-- Broadcast Settings -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wabox_broadcast_delay" class="form-label">
                                    <i class="fas fa-clock me-1"></i>
                                    Delay Antar Pesan (detik) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('wabox_broadcast_delay') is-invalid @enderror"
                                       id="wabox_broadcast_delay"
                                       name="wabox_broadcast_delay"
                                       value="{{ old('wabox_broadcast_delay', $settings['wabox_broadcast_delay']) }}"
                                       min="1"
                                       max="60"
                                       required>
                                @error('wabox_broadcast_delay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Jeda waktu antar pengiriman pesan (1-60 detik). Disarankan 3-5 detik untuk menghindari banned.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wabox_batch_size" class="form-label">
                                    <i class="fas fa-layer-group me-1"></i>
                                    Ukuran Batch <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('wabox_batch_size') is-invalid @enderror"
                                       id="wabox_batch_size"
                                       name="wabox_batch_size"
                                       value="{{ old('wabox_batch_size', $settings['wabox_batch_size']) }}"
                                       min="10"
                                       max="1000"
                                       required>
                                @error('wabox_batch_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Jumlah pesan per batch (10-1000). Sistem akan pause lebih lama setiap selesai batch.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" id="testConnectionBtn" class="btn btn-outline-info me-2">
                                <i class="fas fa-wifi me-1"></i>
                                Test Koneksi
                            </button>

                            <button type="button" id="checkStatusBtn" class="btn btn-outline-success">
                                <i class="fas fa-info-circle me-1"></i>
                                Cek Status Akun
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Test Result -->
        <div id="testResult" class="mt-3" style="display: none;"></div>

        <!-- Current Status -->
        @if($settings['wabox_token'] && $settings['wabox_uid'])
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Status Konfigurasi
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>API URL:</strong> {{ $settings['wabox_api_url'] }}</p>
                        <p><strong>Token:</strong> <span class="text-success">✓ Configured</span></p>
                        <p><strong>Delay Broadcast:</strong> {{ $settings['wabox_broadcast_delay'] }} detik</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>UID:</strong> {{ $settings['wabox_uid'] }}</p>
                        <p><strong>Status:</strong> <span class="text-success">✓ Ready</span></p>
                        <p><strong>Batch Size:</strong> {{ $settings['wabox_batch_size'] }} pesan</p>
                    </div>
                </div>

                <div class="alert alert-light mt-3 mb-3">
                    <h6 class="mb-2">
                        <i class="fas fa-shield-alt me-2 text-success"></i>
                        Perlindungan Anti-Ban Aktif
                    </h6>
                    <small class="text-muted">
                        Sistem akan mengirim pesan dengan jeda {{ $settings['wabox_broadcast_delay'] }} detik antar pesan dan pause ekstra setiap {{ $settings['wabox_batch_size'] }} pesan untuk mencegah banned WhatsApp.
                    </small>
                </div>

                <div class="mt-3">
                    <p class="mb-2"><strong>Fitur Tersedia:</strong></p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="$('#testConnectionBtn').click()">
                            <i class="fas fa-wifi me-1"></i>
                            Test Koneksi
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="$('#checkStatusBtn').click()">
                            <i class="fas fa-info-circle me-1"></i>
                            Cek Status Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfigurasi Belum Lengkap
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">Silakan lengkapi Token dan UID di atas untuk menggunakan fitur broadcast WhatsApp.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#testConnectionBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        const testResult = $('#testResult');

        // Get form values
        const token = $('#wabox_token').val();
        const uid = $('#wabox_uid').val();
        const apiUrl = $('#wabox_api_url').val();

        if (!token || !uid) {
            testResult.html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Mohon isi Token dan UID terlebih dahulu.
                </div>
            `).show();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Testing...');

        // Save current settings temporarily for testing
        $.ajax({
            url: '{{ route("settings.waboxapp.update") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                wabox_token: token,
                wabox_uid: uid,
                wabox_api_url: apiUrl
            },
            success: function(response) {
                // Now test the connection
                $.ajax({
                    url: '{{ route("settings.waboxapp.test") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        testResult.html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Koneksi Berhasil!</strong><br>
                                ${response.message}
                            </div>
                        `).show();
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        testResult.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Koneksi Gagal!</strong><br>
                                ${response.message || 'Terjadi kesalahan saat testing koneksi'}
                            </div>
                        `).show();
                    }
                });
            },
            error: function(xhr) {
                testResult.html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Error!</strong><br>
                        Gagal menyimpan pengaturan untuk testing.
                    </div>
                `).show();
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Check Account Status
    $('#checkStatusBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        const testResult = $('#testResult');

        // Get form values
        const token = $('#wabox_token').val();
        const uid = $('#wabox_uid').val();
        const apiUrl = $('#wabox_api_url').val();

        if (!token || !uid) {
            testResult.html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Mohon isi Token dan UID terlebih dahulu.
                </div>
            `).show();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Checking...');

        // Save current settings temporarily for checking status
        $.ajax({
            url: '{{ route("settings.waboxapp.update") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                wabox_token: token,
                wabox_uid: uid,
                wabox_api_url: apiUrl
            },
            success: function(response) {
                // Now check account status
                $.ajax({
                    url: '{{ route("settings.waboxapp.status") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const data = response.data;
                        testResult.html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Status Akun Berhasil Diambil!</strong>
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>UID:</strong> ${data.uid || 'N/A'}</p>
                                            <p><strong>Alias:</strong> ${data.alias || 'N/A'}</p>
                                            <p><strong>Platform:</strong> ${data.platform || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Battery:</strong> ${data.battery || 'N/A'}%</p>
                                            <p><strong>Plugged:</strong> ${data.plugged === '1' ? 'Yes' : 'No'}</p>
                                            <p><strong>Locale:</strong> ${data.locale || 'N/A'}</p>
                                        </div>
                                    </div>
                                    ${data.hook_url ? '<p><strong>Hook URL:</strong> ' + data.hook_url + '</p>' : ''}
                                </div>
                            </div>
                        `).show();
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        testResult.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Gagal Mengambil Status!</strong><br>
                                ${response.message || 'Terjadi kesalahan saat mengambil status akun'}
                            </div>
                        `).show();
                    }
                });
            },
            error: function(xhr) {
                testResult.html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Error!</strong><br>
                        Gagal menyimpan pengaturan untuk cek status.
                    </div>
                `).show();
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
