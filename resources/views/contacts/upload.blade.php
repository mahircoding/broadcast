@extends('layouts.app')

@section('title', 'Upload Kontak CSV')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-upload me-2"></i>
                    Upload Kontak dari CSV
                </h5>
            </div>
            <div class="card-body">
                <!-- Instructions -->
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Format CSV yang Diterima
                    </h6>
                    <p class="mb-2">File CSV harus memiliki kolom dalam urutan berikut:</p>
                    <ol class="mb-2">
                        <li><strong>Nama</strong> (wajib)</li>
                        <li><strong>Nomor HP</strong> (wajib)</li>
                        <li><strong>Email</strong> (opsional)</li>
                        <li><strong>Grup</strong> (opsional)</li>
                        <li><strong>Catatan</strong> (opsional)</li>
                    </ol>
                    <p class="mb-0">
                        <small>
                            Contoh: "John Doe,08123456789,john@email.com,Keluarga,Teman dari kantor"
                        </small>
                    </p>
                </div>

                <!-- Sample CSV Download -->
                <div class="mb-4">
                    <a href="data:text/csv;charset=utf-8,Nama%2CNomor%20HP%2CEmail%2CGrup%2CCatatan%0AJohn%20Doe%2C08123456789%2Cjohn%40email.com%2CKeluarga%2CTeman%20dari%20kantor%0AJane%20Smith%2C%2B6281234567890%2Cjane%40email.com%2CKerja%2CRekan%20bisnis%0ABudi%20Santoso%2C08123456788%2C%2CKeluarga%2C"
                       download="sample_contacts.csv"
                       class="btn btn-outline-secondary">
                        <i class="fas fa-download me-1"></i>
                        Download Contoh CSV
                    </a>
                </div>

                <form method="POST" action="{{ route('contacts.process-upload') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">File CSV <span class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control @error('csv_file') is-invalid @enderror"
                               id="csv_file"
                               name="csv_file"
                               accept=".csv,.txt"
                               required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Maksimal ukuran file: 2MB. Format yang diterima: .csv, .txt
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   value="1"
                                   id="has_header"
                                   name="has_header"
                                   {{ old('has_header', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_header">
                                File CSV memiliki header (baris pertama adalah nama kolom)
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Perhatian
                        </h6>
                        <ul class="mb-0">
                            <li>Kontak dengan nomor HP yang sudah ada akan dilewati</li>
                            <li>Format nomor HP akan otomatis disesuaikan (menambah +62 jika perlu)</li>
                            <li>Email yang tidak valid akan menyebabkan baris tersebut dilewati</li>
                            <li>Proses upload tidak dapat dibatalkan setelah dimulai</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>
                            Upload dan Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Upload Results -->
        @if(session('csv_results'))
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Hasil Upload Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    @php $results = session('csv_results') @endphp

                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ $results['success'] }}</h4>
                                <small class="text-muted">Berhasil Ditambahkan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">{{ $results['skipped'] }}</h4>
                                <small class="text-muted">Dilewati (Duplikat)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">{{ $results['errors'] }}</h4>
                                <small class="text-muted">Error</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $results['success'] + $results['skipped'] + $results['errors'] }}</h4>
                                <small class="text-muted">Total Baris</small>
                            </div>
                        </div>
                    </div>

                    @if(!empty($results['error_details']))
                        <div class="mt-3">
                            <h6>Detail Error:</h6>
                            <ul class="list-unstyled">
                                @foreach(array_slice($results['error_details'], 0, 10) as $error)
                                    <li><small class="text-danger">• {{ $error }}</small></li>
                                @endforeach
                                @if(count($results['error_details']) > 10)
                                    <li><small class="text-muted">... dan {{ count($results['error_details']) - 10 }} error lainnya</small></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
