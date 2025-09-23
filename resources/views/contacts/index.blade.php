@extends('layouts.app')

@section('title', 'Manajemen Kontak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-address-book me-2"></i>
        Manajemen Kontak
    </h1>
    <div>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary me-2">
            <i class="fas fa-plus me-1"></i>
            Tambah Kontak
        </a>
        <a href="{{ route('contacts.upload') }}" class="btn btn-success">
            <i class="fas fa-upload me-1"></i>
            Upload CSV
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('contacts.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Kontak</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Nama, nomor, atau email...">
                </div>
                <div class="col-md-3">
                    <label for="group" class="form-label">Grup</label>
                    <select class="form-select" id="group" name="group">
                        <option value="">Semua Grup</option>
                        @foreach($groups as $group)
                            <option value="{{ $group }}" {{ request('group') === $group ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Cari
                    </button>
                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Export Button -->
@if($contacts->count() > 0)
<div class="mb-3">
    <a href="{{ route('contacts.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
       class="btn btn-outline-success">
        <i class="fas fa-download me-1"></i>
        Export ke CSV
    </a>
</div>
@endif

<!-- Contacts Table -->
<div class="card">
    <div class="card-body">
        @if($contacts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Email</th>
                            <th>Grup</th>
                            <th>Status</th>
                            <th>Ditambahkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $contact)
                            <tr>
                                <td>
                                    <strong>{{ $contact->name }}</strong>
                                    @if($contact->notes)
                                        <br><small class="text-muted">{{ Str::limit($contact->notes, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-monospace">{{ $contact->phone_number }}</span>
                                </td>
                                <td>
                                    {{ $contact->email ?: '-' }}
                                </td>
                                <td>
                                    @if($contact->group)
                                        <span class="badge bg-secondary">{{ $contact->group }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($contact->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $contact->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('contacts.edit', $contact) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $contact->id }}, '{{ $contact->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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
                        Menampilkan {{ $contacts->firstItem() }} - {{ $contacts->lastItem() }}
                        dari {{ $contacts->total() }} kontak
                    </small>
                </div>
                <div>
                    {{ $contacts->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada kontak ditemukan</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'group', 'status']))
                        Coba ubah kriteria pencarian atau
                        <a href="{{ route('contacts.index') }}">hapus filter</a>
                    @else
                        Mulai dengan <a href="{{ route('contacts.create') }}">menambah kontak baru</a>
                        atau <a href="{{ route('contacts.upload') }}">upload file CSV</a>
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kontak <strong id="contactName"></strong>?</p>
                <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(contactId, contactName) {
    document.getElementById('contactName').textContent = contactName;
    document.getElementById('deleteForm').action = `/contacts/${contactId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
