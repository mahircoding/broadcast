@extends('layouts.app')

@section('title', 'Edit Kontak')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit Kontak: {{ $contact->name }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('contacts.update', $contact) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $contact->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('phone_number') is-invalid @enderror"
                                   id="phone_number"
                                   name="phone_number"
                                   value="{{ old('phone_number', $contact->phone_number) }}"
                                   placeholder="08xxxxxxxxxx atau +62xxxxxxxxx"
                                   required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Format: 08xxxxxxxxxx atau +62xxxxxxxxx
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $contact->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="group" class="form-label">Grup</label>
                            <input type="text"
                                   class="form-control @error('group') is-invalid @enderror"
                                   id="group"
                                   name="group"
                                   value="{{ old('group', $contact->group) }}"
                                   list="groupsList"
                                   placeholder="Masukkan nama grup">
                            <datalist id="groupsList">
                                @foreach($groups as $group)
                                    <option value="{{ $group }}">
                                @endforeach
                            </datalist>
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="3"
                                  placeholder="Tambahkan catatan tambahan (opsional)">{{ old('notes', $contact->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   value="1"
                                   id="is_active"
                                   name="is_active"
                                   {{ old('is_active', $contact->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kontak aktif
                            </label>
                        </div>
                        <div class="form-text">
                            Kontak yang tidak aktif tidak akan menerima broadcast
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Kontak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Informasi Kontak</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Ditambahkan:</strong> {{ $contact->created_at->format('d F Y H:i') }}</p>
                        <p><strong>Terakhir diupdate:</strong> {{ $contact->updated_at->format('d F Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Format HP:</strong> {{ $contact->formatted_phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Format phone number as user types
    $('#phone_number').on('input', function() {
        let value = $(this).val();

        // Remove all non-numeric characters except +
        value = value.replace(/[^\d+]/g, '');

        // Update the input value
        $(this).val(value);
    });
});
</script>
@endpush
