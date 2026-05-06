@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0 font-weight-bold" style="color: #1a6b3c;">
                <i class="fas fa-user-circle mr-2" style="color: #f6c90e;"></i>
                Profil Saya
            </h1>
            <small class="text-muted">Lihat dan kelola informasi akun Anda</small>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('status') === 'profile-updated')
    <div id="autoAlert" class="alert alert-success alert-dismissible fade show shadow-sm"
        style="border-left: 4px solid #1a6b3c; border-radius: 8px;">
        <i class="fas fa-check-circle mr-2"></i>Profil berhasil diperbarui.
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <script>
        setTimeout(function() {
            let a = document.getElementById('autoAlert');
            if (a) { a.style.transition = 'opacity 0.5s'; a.style.opacity = '0'; setTimeout(() => a.remove(), 500); }
        }, 4000);
    </script>
    @endif

    <div class="row">

        {{-- ===== KIRI: CARD PROFIL ===== --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow" style="border: none; border-radius: 12px; overflow: hidden;">

                {{-- Header card --}}
                <div class="card-header text-center py-4"
                    style="background: linear-gradient(135deg, #1a6b3c, #145c32);">

                    {{-- Avatar inisial --}}
                    <div style="
                        width: 80px; height: 80px; border-radius: 50%;
                        background: linear-gradient(135deg, #f6c90e, #e0b800);
                        display: flex; align-items: center; justify-content: center;
                        font-size: 2rem; font-weight: bold; color: #1a6b3c;
                        margin: 0 auto 12px; border: 3px solid rgba(255,255,255,0.3);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h5 class="font-weight-bold text-white mb-1">{{ $user->name }}</h5>
                    <span class="badge px-3 py-1"
                        style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px; font-size: 0.75rem;">
                        {{ $user->role === 'admin_pusat' ? '⚙️ Admin Pusat' : '🏢 Admin Satker' }}
                    </span>
                </div>

                {{-- Info profil --}}
                <div class="card-body p-0">

                    <div class="px-4 py-3 border-bottom">
                        <div class="text-xs text-muted mb-1">Username</div>
                        <div class="font-weight-bold small">
                            <code style="background: #f0faf4; color: #1a6b3c; padding: 2px 8px; border-radius: 4px;">
                                {{ $user->username ?? '-' }}
                            </code>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-bottom">
                        <div class="text-xs text-muted mb-1">Email</div>
                        <div class="font-weight-bold small">
                            <i class="fas fa-envelope mr-1 text-muted"></i>
                            {{ $user->email }}
                        </div>
                    </div>

                    <div class="px-4 py-3 border-bottom">
                        <div class="text-xs text-muted mb-1">Kontak</div>
                        <div class="font-weight-bold small">
                            <i class="fas fa-phone mr-1 text-muted"></i>
                            {{ $user->kontak ?? '-' }}
                        </div>
                    </div>

                    @if($user->role === 'admin_satker')
                    <div class="px-4 py-3 border-bottom">
                        <div class="text-xs text-muted mb-1">Satker</div>
                        <div class="font-weight-bold small">
                            <i class="fas fa-building mr-1 text-muted"></i>
                            {{ optional($user->satker)->nama_satker ?? '-' }}
                        </div>
                    </div>
                    @endif

                    <div class="px-4 py-3">
                        <div class="text-xs text-muted mb-1">Bergabung Sejak</div>
                        <div class="font-weight-bold small">
                            <i class="fas fa-calendar mr-1 text-muted"></i>
                            {{ $user->created_at->format('d M Y') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== KANAN: FORM UPDATE ===== --}}
        <div class="col-lg-8">

            {{-- FORM UPDATE PROFIL --}}
            <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

                <div class="card-header" style="background: linear-gradient(90deg, #1a6b3c, #145c32); padding: 14px 20px;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-edit mr-2" style="color: #f6c90e;"></i>Update Informasi Profil
                    </h6>
                </div>

                <div class="card-body" style="background: #f8fff9;">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Nama Lengkap</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        style="border-radius: 8px;"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Username</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="border-radius: 8px 0 0 8px;">@</span>
                                        </div>
                                        <input type="text" name="username"
                                            class="form-control @error('username') is-invalid @enderror"
                                            style="border-radius: 0 8px 8px 0;"
                                            value="{{ old('username', $user->username) }}">
                                    </div>
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        style="border-radius: 8px;"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Kontak / No. HP</label>
                                    <input type="text" name="kontak"
                                        class="form-control"
                                        style="border-radius: 8px;"
                                        value="{{ old('kontak', $user->kontak) }}"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm font-weight-bold"
                            style="background: #1a6b3c; color: white; border-radius: 6px; padding: 8px 20px;">
                            <i class="fas fa-save mr-1"></i>Simpan Perubahan
                        </button>

                    </form>
                </div>
            </div>

            {{-- FORM GANTI PASSWORD --}}
            <div class="card shadow mb-4" style="border: none; border-radius: 12px; overflow: hidden;">

                <div class="card-header" style="background: linear-gradient(90deg, #004085, #0062cc); padding: 14px 20px;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-key mr-2" style="color: #f6c90e;"></i>Ganti Password
                    </h6>
                </div>

                <div class="card-body" style="background: #f0f4ff;">

                    @if(session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show"
                        style="border-left: 4px solid #004085; border-radius: 8px;">
                        <i class="fas fa-check-circle mr-2"></i>Password berhasil diperbarui.
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                style="border-radius: 8px;"
                                placeholder="Password saat ini">
                            @error('current_password', 'updatePassword')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Password Baru</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                        style="border-radius: 8px;"
                                        placeholder="Minimal 8 karakter">
                                    @error('password', 'updatePassword')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control"
                                        style="border-radius: 8px;"
                                        placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm font-weight-bold"
                            style="background: #004085; color: white; border-radius: 6px; padding: 8px 20px;">
                            <i class="fas fa-key mr-1"></i>Ganti Password
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection