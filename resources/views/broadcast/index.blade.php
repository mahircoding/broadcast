@extends('layouts.app')

@section('title', 'Kirim Broadcast')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-bullhorn me-2"></i>
            Kirim Broadcast WhatsApp
        </h1>
    </div>
</div>

<div class="row">
    <!-- Broadcast Form -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-paper-plane me-2"></i>
                    Form Broadcast
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('broadcast.send') }}" id="broadcastForm">
                    @csrf

                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan Broadcast <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror"
                                  id="message"
                                  name="message"
                                  rows="6"
                                  maxlength="1000"
                                  placeholder="Tulis pesan broadcast Anda di sini..."
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="messageCounter">0</span>/1000 karakter
                        </div>
                    </div>

                    <!-- Recipient Selection -->
                    <div class="mb-3">
                        <label class="form-label">Penerima <span class="text-danger">*</span></label>

                        <!-- Selection Type -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="selection_type" id="individual" value="individual" checked>
                            <label class="btn btn-outline-primary" for="individual">Pilih Individu</label>

                            <input type="radio" class="btn-check" name="selection_type" id="group" value="group">
                            <label class="btn btn-outline-primary" for="group">Pilih Grup</label>

                            <input type="radio" class="btn-check" name="selection_type" id="all" value="all">
                            <label class="btn btn-outline-primary" for="all">Semua Kontak</label>
                        </div>

                        <!-- Individual Selection -->
                        <div id="individualSelection">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="contactSearch" placeholder="Cari kontak...">
                            </div>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label fw-bold" for="selectAll">
                                        Pilih Semua
                                    </label>
                                </div>
                                <hr>
                                @foreach($contacts as $contact)
                                    <div class="form-check contact-item" data-name="{{ strtolower($contact->name) }}" data-phone="{{ $contact->phone_number }}">
                                        <input class="form-check-input contact-checkbox"
                                               type="checkbox"
                                               value="{{ $contact->id }}"
                                               id="contact_{{ $contact->id }}"
                                               name="recipients[]">
                                        <label class="form-check-label" for="contact_{{ $contact->id }}">
                                            <strong>{{ $contact->name }}</strong>
                                            <br><small class="text-muted">{{ $contact->phone_number }}</small>
                                            @if($contact->group)
                                                <span class="badge bg-secondary ms-1">{{ $contact->group }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Group Selection -->
                        <div id="groupSelection" style="display: none;">
                            <select class="form-select" id="groupSelect" name="group">
                                <option value="">Pilih Grup</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                            <div id="groupContacts" class="mt-2"></div>
                        </div>

                        @error('recipients')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Personalization Option -->
                    <div class="mb-3">
                        <div class="card border-info">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-tag me-2"></i>
                                    Personalisasi Pesan
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="use_personalization" name="use_personalization" value="1">
                                    <label class="form-check-label" for="use_personalization">
                                        <strong>Aktifkan personalisasi pesan</strong>
                                        <br><small class="text-muted">Gunakan parameter khusus untuk menyapa penerima dengan nama mereka</small>
                                    </label>
                                </div>
                                
                                <div id="personalizationHelp" style="display: none;" class="mt-3">
                                    <div class="alert alert-info mb-0">
                                        <h6><i class="fas fa-info-circle me-2"></i>Parameter yang bisa digunakan:</h6>
                                        <ul class="mb-2">
                                            <li><code>{name}</code> atau <code>{nama}</code> - Nama kontak</li>
                                            <li><code>{phone}</code> atau <code>{telepon}</code> - Nomor telepon</li>
                                            <li><code>{group}</code> atau <code>{grup}</code> - Grup kontak</li>
                                            <li><code>{email}</code> - Email kontak (jika ada)</li>
                                            <li><code>{address}</code> atau <code>{alamat}</code> - Alamat kontak (jika ada)</li>
                                        </ul>
                                        <p class="mb-0">
                                            <strong>Contoh:</strong><br>
                                            "Halo {name}, terima kasih telah bergabung dengan grup {group}!"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="selectedCount">0</span> kontak dipilih
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>
                            Kirim Broadcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-zap me-2"></i>
                    Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="sendToGroup()">
                        <i class="fas fa-users me-1"></i>
                        Kirim ke Grup
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="sendToAll()">
                        <i class="fas fa-globe me-1"></i>
                        Kirim ke Semua
                    </button>
                    <a href="{{ route('broadcast.history') }}" class="btn btn-outline-info">
                        <i class="fas fa-history me-1"></i>
                        Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Broadcasts -->
        @if($recentBroadcasts->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Broadcast Terbaru
                </h6>
            </div>
            <div class="card-body">
                @foreach($recentBroadcasts as $broadcast)
                    <div class="border-bottom pb-2 mb-2">
                        <p class="mb-1">
                            <small>{{ Str::limit($broadcast->message, 50) }}</small>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $broadcast->created_at->diffForHumans() }}
                            </small>
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
                <a href="{{ route('broadcast.history') }}" class="btn btn-sm btn-outline-primary w-100">
                    Lihat Semua
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Group Selection Modal -->
<div class="modal fade" id="groupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim ke Grup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('broadcast.send-to-group') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="groupMessage" class="form-label">Pesan</label>
                        <textarea class="form-control" id="groupMessage" name="message" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="groupSelection" class="form-label">Pilih Grup</label>
                        <select class="form-select" id="groupSelection" name="group" required>
                            <option value="">Pilih Grup</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="group_use_personalization" name="use_personalization" value="1">
                            <label class="form-check-label" for="group_use_personalization">
                                Aktifkan personalisasi pesan (gunakan {name}, {group}, dll.)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send to All Modal -->
<div class="modal fade" id="allModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim ke Semua Kontak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('broadcast.send-to-all') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="allMessage" class="form-label">Pesan</label>
                        <textarea class="form-control" id="allMessage" name="message" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="all_use_personalization" name="use_personalization" value="1">
                            <label class="form-check-label" for="all_use_personalization">
                                Aktifkan personalisasi pesan (gunakan {name}, {group}, dll.)
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pesan akan dikirim ke semua kontak aktif ({{ $contacts->count() }} kontak).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim ke Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Message counter
    $('#message').on('input', function() {
        const length = $(this).val().length;
        $('#messageCounter').text(length);
    });

    // Contact search
    $('#contactSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('.contact-item').each(function() {
            const name = $(this).data('name');
            const phone = $(this).data('phone');
            if (name.includes(search) || phone.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Select all checkbox
    $('#selectAll').change(function() {
        $('.contact-checkbox:visible').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox change
    $('.contact-checkbox').change(function() {
        updateSelectedCount();
    });

    // Selection type change
    $('input[name="selection_type"]').change(function() {
        const type = $(this).val();

        if (type === 'individual') {
            $('#individualSelection').show();
            $('#groupSelection').hide();
        } else if (type === 'group') {
            $('#individualSelection').hide();
            $('#groupSelection').show();
        } else {
            $('#individualSelection').hide();
            $('#groupSelection').hide();
        }

        updateSelectedCount();
    });

    // Group selection change
    $('#groupSelect').change(function() {
        const group = $(this).val();
        if (group) {
            $.get('{{ route("broadcast.get-contacts-by-group") }}', { group: group })
                .done(function(contacts) {
                    let html = '<div class="alert alert-info mt-2">';
                    html += `<strong>${contacts.length} kontak</strong> dalam grup "${group}"`;
                    html += '</div>';
                    $('#groupContacts').html(html);
                });
        } else {
            $('#groupContacts').html('');
        }
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const type = $('input[name="selection_type"]:checked').val();
        let count = 0;

        if (type === 'individual') {
            count = $('.contact-checkbox:checked').length;
        } else if (type === 'group') {
            const group = $('#groupSelect').val();
            if (group) {
                // Get count from server or estimate
                count = "~";
            }
        } else if (type === 'all') {
            count = {{ $contacts->count() }};
        }

        $('#selectedCount').text(count);
    }

    // Personalization toggle
    $('#use_personalization').change(function() {
        if ($(this).is(':checked')) {
            $('#personalizationHelp').slideDown();
        } else {
            $('#personalizationHelp').slideUp();
        }
    });

    // Form submission
    $('#broadcastForm').submit(function(e) {
        const type = $('input[name="selection_type"]:checked').val();

        if (type === 'individual') {
            const selected = $('.contact-checkbox:checked').length;
            if (selected === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu kontak.');
                return false;
            }
        } else if (type === 'group') {
            const group = $('#groupSelect').val();
            if (!group) {
                e.preventDefault();
                alert('Silakan pilih grup.');
                return false;
            }
            // Change form action
            $(this).attr('action', '{{ route("broadcast.send-to-group") }}');
            $('<input>').attr({
                type: 'hidden',
                name: 'group',
                value: group
            }).appendTo(this);
        } else if (type === 'all') {
            // Change form action
            $(this).attr('action', '{{ route("broadcast.send-to-all") }}');
        }

        // Show loading
        $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...');
    });
});

function sendToGroup() {
    $('#groupModal').modal('show');
}

function sendToAll() {
    $('#allModal').modal('show');
}
</script>
@endpush
