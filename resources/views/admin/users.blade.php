@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-users-cog mr-2" style="color: #f6c90e;"></i>
                Manajemen User
            </h1>
            <small class="text-muted">Kelola akun Admin Satker</small>
        </div>
        <button class="btn btn-sm font-weight-bold shadow-sm mt-2 mt-sm-0"
            data-toggle="modal" data-target="#modalTambahUser"
            style="background: linear-gradient(135deg, #1a6b3c, #145c32); color: white; border-radius: 8px; padding: 8px 16px;">
            <i class="fas fa-plus mr-1"></i> Tambah User
        </button>
    </div>

    {{-- TABEL USER --}}
    <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list mr-2" style="color: #f6c90e;"></i>Daftar Admin Satker
            </h6>
            <span class="badge"
                style="background: rgba(255,255,255,0.15); color: white; border-radius: 20px; padding: 4px 12px;">
                {{ $users->count() }} user
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelUsers" class="table table-hover mb-0">
                    <thead style="background: #f8fff9;">
                        <tr>
                            <th class="border-0 pl-4" style="color: #1a6b3c; font-size: 0.82rem;">No</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Nama</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Username</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Email</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Kontak</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Satker</th>
                            <th class="border-0" style="color: #1a6b3c; font-size: 0.82rem;">Dibuat</th>
                            <th class="border-0 text-center" style="color: #1a6b3c; font-size: 0.82rem; width: 80px;" data-no-sort>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                        <tr style="border-left: 3px solid transparent; transition: 0.2s;"
                            onmouseover="this.style.borderLeft='3px solid #1a6b3c'"
                            onmouseout="this.style.borderLeft='3px solid transparent'">

                            <td class="pl-4 align-middle text-muted small">{{ $i + 1 }}</td>

                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="
                                        width: 36px; height: 36px; border-radius: 50%;
                                        background: linear-gradient(135deg, #1a6b3c, #f6c90e);
                                        display: flex; align-items: center; justify-content: center;
                                        color: white; font-weight: bold; font-size: 0.85rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-weight-bold small">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="align-middle">
                                <code style="background: #f0faf4; color: #1a6b3c; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    {{ $user->username ?? '-' }}
                                </code>
                            </td>

                            <td class="align-middle small text-muted">{{ $user->email }}</td>

                            <td class="align-middle small text-muted">
                                {{ $user->kontak ?? '-' }}
                            </td>

                            <td class="align-middle">
                                <span class="badge"
                                    style="background: #e8f5ee; color: #1a6b3c; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem;">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ optional($user->satker)->nama_satker ?? '-' }}
                                </span>
                            </td>

                            <td class="align-middle text-muted small" data-sort="{{ $user->created_at }}">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td class="align-middle text-center">
                                <div class="dropdown no-arrow">
                                    <button class="btn-action-dots dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-smooth animated--fade-in">
                                        <a class="dropdown-item small font-weight-bold" href="#" data-toggle="modal" data-target="#modalEditUser-{{ $user->id }}">
                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Edit User
                                        </a>
                                        <a class="dropdown-item small font-weight-bold" href="#" data-toggle="modal" data-target="#modalResetPass-{{ $user->id }}">
                                            <i class="fas fa-key fa-sm fa-fw mr-2 text-primary"></i> Reset Password
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item small font-weight-bold text-danger" href="#" onclick="event.preventDefault(); konfirmasiHapusUser('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="fas fa-trash fa-sm fa-fw mr-2"></i> Hapus User
                                        </a>
                                    </div>
                                    <form id="formHapusUser-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>

                        </tr>

                        {{-- MODAL EDIT USER --}}
                        <div class="modal fade" id="modalEditUser-{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                                    <div class="modal-header"
                                        style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                                        <h5 class="modal-title font-weight-bold text-white">
                                            <i class="fas fa-edit mr-2" style="color: #f6c90e;"></i>Edit User
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="formEditUser-{{ $user->id }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body" style="background: #f8fff9;">
                                            <div class="form-group">
                                                <label class="small font-weight-bold text-muted">Nama Lengkap</label>
                                                <input type="text" name="name"
                                                    class="form-control"
                                                    style="border-radius: 8px;"
                                                    value="{{ $user->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="small font-weight-bold text-muted">Username</label>
                                                <input type="text" name="username"
                                                    class="form-control"
                                                    style="border-radius: 8px;"
                                                    value="{{ $user->username }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="small font-weight-bold text-muted">Email</label>
                                                <input type="email" name="email"
                                                    class="form-control"
                                                    style="border-radius: 8px;"
                                                    value="{{ $user->email }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="small font-weight-bold text-muted">Kontak</label>
                                                <input type="text" name="kontak"
                                                    class="form-control"
                                                    style="border-radius: 8px;"
                                                    value="{{ $user->kontak }}">
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="filter-label">Satker</label>
                                                <div class="custom-dropdown-container">
                                                    <input type="hidden" name="satker_id" id="hidden_satker_edit_{{ $user->id }}" value="{{ $user->satker_id }}">
                                                    <button type="button" class="interactive-field text-left d-flex justify-content-between align-items-center" 
                                                            onclick="toggleCustomDropdown('satker_edit_{{ $user->id }}')">
                                                        <span id="labelSatkerEdit_{{ $user->id }}" class="text-truncate mr-2">
                                                            {{ $user->satker->nama_satker ?? 'Pilih Satker' }}
                                                        </span>
                                                        <i class="material-icons dropdown-toggle-icon" style="font-size:18px; color:var(--c-theme-primary);" id="icon-satker_edit_{{ $user->id }}">expand_more</i>
                                                    </button>
                                                    <div class="custom-dropdown-menu shadow-lg d-none" id="menu-satker_edit_{{ $user->id }}">
                                                        <div class="p-2 border-bottom sticky-top bg-white">
                                                            <input type="text" class="form-control form-control-sm" placeholder="Cari satker..." oninput="filterDropdownList('satker_edit_{{ $user->id }}', this.value)" onclick="event.stopPropagation()">
                                                        </div>
                                                        <div class="list-wrapper">
                                                        @foreach($satkers as $s)
                                                            <div class="dropdown-item-custom py-2 px-3 cursor-pointer" data-search="{{ strtolower($s->nama_satker) }}" onclick="selectDropdownOption('satker_edit_{{ $user->id }}', '{{ $s->id }}', '{{ $s->nama_satker }}', 'hidden_satker_edit_{{ $user->id }}', 'labelSatkerEdit_{{ $user->id }}')">
                                                                <div class="small font-weight-bold">{{ $s->nama_satker }}</div>
                                                            </div>
                                                        @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background: #f8fff9;">
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                data-dismiss="modal" style="border-radius: 6px;">
                                                <i class="fas fa-times mr-1"></i>Batal
                                            </button>
                                            <button type="submit" class="btn btn-sm font-weight-bold"
                                                style="background: #1a6b3c; color: white; border-radius: 6px; padding: 6px 16px;">
                                                <i class="fas fa-save mr-1"></i>Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL RESET PASSWORD --}}
                        <div class="modal fade" id="modalResetPass-{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                                    <div class="modal-header"
                                        style="background: linear-gradient(90deg, #004085, #0062cc);">
                                        <h5 class="modal-title font-weight-bold text-white">
                                            <i class="fas fa-key mr-2" style="color: #f6c90e;"></i>
                                            Reset Password — {{ $user->name }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}" id="formResetPass-{{ $user->id }}">
                                        @csrf
                                        <div class="modal-body" style="background: #f0f4ff;">

                                            <div class="alert py-2 mb-3"
                                                style="background: #cce5ff; border: 1px solid #b8daff; border-radius: 8px; font-size: 0.82rem;">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Password baru akan langsung aktif setelah disimpan.
                                            </div>

                                            <div class="form-group">
                                                <label class="small font-weight-bold text-muted">Password Baru</label>
                                                <input type="password" name="password"
                                                    class="form-control"
                                                    style="border-radius: 8px;"
                                                    placeholder="Minimal 8 karakter" required>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="small font-weight-bold text-muted">Konfirmasi Password</label>
                                                <input type="password" name="password_confirmation"
                                                    class="form-control"
                                                    style="border-radius: 8px;"
                                                    placeholder="Ulangi password" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background: #f0f4ff;">
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                data-dismiss="modal" style="border-radius: 6px;">
                                                <i class="fas fa-times mr-1"></i>Batal
                                            </button>
                                            <button type="submit" class="btn btn-sm font-weight-bold"
                                                style="background: #004085; color: white; border-radius: 6px; padding: 6px 16px;">
                                                <i class="fas fa-key mr-1"></i>Reset Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block" style="color: #d1e7d8;"></i>
                                Belum ada Admin Satker terdaftar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">

            <div class="modal-header"
                style="background: linear-gradient(90deg, #1a6b3c, #145c32);">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2" style="color: #f6c90e;"></i>Tambah Admin Satker
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" id="formTambahUser">
                @csrf
                <div class="modal-body" style="background: #f8fff9;">

                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Nama Lengkap</label>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            style="border-radius: 8px;"
                            placeholder="Nama lengkap"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px;">@</span>
                            </div>
                            <input type="text" name="username"
                                class="form-control @error('username') is-invalid @enderror"
                                style="border-radius: 0 8px 8px 0;"
                                placeholder="username_satker"
                                value="{{ old('username') }}" required>
                        </div>
                        <small class="text-muted">Huruf, angka, dash, underscore. Dipakai untuk login.</small>
                        @error('username')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Email</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            style="border-radius: 8px;"
                            placeholder="email@contoh.com"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Kontak / No. HP</label>
                        <input type="text" name="kontak"
                            class="form-control"
                            style="border-radius: 8px;"
                            placeholder="08xxxxxxxxxx"
                            value="{{ old('kontak') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="filter-label">Satker</label>
                        <div class="custom-dropdown-container">
                            <input type="hidden" name="satker_id" id="hidden_satker_create" value="{{ old('satker_id') }}">
                            <button type="button" class="interactive-field text-left d-flex justify-content-between align-items-center @error('satker_id') is-invalid @enderror" 
                                    id="btnSatkerCreate" onclick="toggleCustomDropdown('satker_create')">
                                <span id="labelSatkerCreate" class="text-truncate mr-2">
                                    @if(old('satker_id'))
                                        {{ $satkers->firstWhere('id', old('satker_id'))->nama_satker ?? '-- Pilih Satker --' }}
                                    @else
                                        -- Pilih Satker --
                                    @endif
                                </span>
                                <i class="material-icons dropdown-toggle-icon" style="font-size:18px; color:var(--c-theme-primary);" id="icon-satker_create">expand_more</i>
                            </button>
                            <div class="custom-dropdown-menu shadow-lg d-none" id="menu-satker_create">
                                <div class="p-2 border-bottom sticky-top bg-white">
                                    <input type="text" class="form-control form-control-sm" placeholder="Cari satker..." oninput="filterDropdownList('satker_create', this.value)" onclick="event.stopPropagation()">
                                </div>
                                <div class="list-wrapper">
                                @foreach($satkers as $s)
                                    <div class="dropdown-item-custom py-2 px-3 cursor-pointer" data-search="{{ strtolower($s->nama_satker) }}" onclick="selectDropdownOption('satker_create', '{{ $s->id }}', '{{ $s->nama_satker }}', 'hidden_satker_create', 'labelSatkerCreate')">
                                        <div class="small font-weight-bold">{{ $s->nama_satker }}</div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        @error('satker_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Password</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            style="border-radius: 8px;"
                            placeholder="Minimal 8 karakter" required>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="form-control"
                            style="border-radius: 8px;"
                            placeholder="Ulangi password" required>
                    </div>

                </div>

                <div class="modal-footer" style="background: #f8fff9;">
                    <button type="button" class="btn btn-sm btn-secondary"
                        data-dismiss="modal" style="border-radius: 6px;">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-sm font-weight-bold"
                        style="background: #1a6b3c; color: white; border-radius: 6px; padding: 6px 16px;">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    LapauTable.init('tabelUsers', {
        pageSize: 10,
        sortDir: 'desc'
    });
});

// Konfirmasi Delete User
function konfirmasiHapusUser(userId, userName) {
    swalConfirm({
        title: 'Hapus User?',
        text: `Apakah Anda yakin ingin menghapus user "${userName}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        confirmText: 'Ya, Hapus',
        confirmColor: '#e74a3b'
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('formHapusUser-' + userId).submit();
        }
    });
}
</script>
@endpush