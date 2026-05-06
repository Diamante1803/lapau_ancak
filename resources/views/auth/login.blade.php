<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login — Lapau Ancak</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(160deg, #1a6b3c 0%, #145c32 40%, #0f4526 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }

        /* Subtle dot pattern overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 0 16px;
            position: relative;
            z-index: 1;
        }

        /* Brand header */
        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            backdrop-filter: blur(8px);
        }

        .brand-icon i {
            font-size: 1.5rem;
            color: #f6c90e;
        }

        .brand-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1.5px;
            margin: 0;
        }

        .brand-sub {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.55);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Card */
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow:
                0 20px 60px rgba(5, 20, 10, 0.35),
                0 4px 16px rgba(5, 20, 10, 0.15);
        }

        /* Card top accent */
        .card-accent {
            height: 4px;
            background: linear-gradient(90deg, #1a6b3c, #2d9e5f, #f6c90e, #2d9e5f, #1a6b3c);
            background-size: 200% auto;
            animation: slideGradient 4s linear infinite;
        }

        @keyframes slideGradient {
            0%   { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .card-body-inner {
            padding: 36px 36px 28px;
        }

        /* Heading */
        .card-heading {
            margin-bottom: 28px;
        }

        .card-heading h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a6b3c;
            margin: 0 0 4px;
        }

        .card-heading p {
            font-size: 0.82rem;
            color: #94a3b8;
            margin: 0;
        }

        /* Label */
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 6px;
            display: block;
        }

        /* Input */
        .input-wrap {
            position: relative;
            margin-bottom: 18px;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #b2d8c0;
            font-size: 0.85rem;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.88rem;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
        }

        .form-input::placeholder { color: #c0cfe0; }

        .form-input:focus {
            border-color: #2d9e5f;
            background: #f0faf4;
            box-shadow: 0 0 0 3px rgba(45,158,95,0.12);
        }

        .form-input.is-invalid {
            border-color: #fca5a5;
            background: #fff8f8;
        }

        /* Password toggle */
        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.85rem;
            padding: 0;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: #1a6b3c; }

        /* Error message */
        .error-msg {
            font-size: 0.76rem;
            color: #ef4444;
            margin-top: -12px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Remember + forgot */
        .form-row-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            font-size: 0.82rem;
            color: #64748b;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            accent-color: #1a6b3c;
            width: 15px;
            height: 15px;
        }

        .forgot-link {
            font-size: 0.82rem;
            color: #2d9e5f;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #1a6b3c; text-decoration: none; }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #1a6b3c, #145c32);
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(26,107,60,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #2d9e5f, #1a6b3c);
            box-shadow: 0 6px 24px rgba(26,107,60,0.4);
            transform: translateY(-1px);
        }

        .btn-submit:active { transform: translateY(0); }

        /* Divider */
        .card-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 20px 0;
        }

        /* Footer */
        .card-footer-inner {
            text-align: center;
            padding: 0 36px 24px;
        }

        .card-footer-inner p {
            font-size: 0.73rem;
            color: #94a3b8;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .card-footer-inner i { color: #b2d8c0; }

        /* Bottom copyright */
        .page-footer {
            text-align: center;
            margin-top: 20px;
        }

        .page-footer p {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
            margin: 0;
        }

        /* Alert */
        .alert-custom {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success-custom {
            background: #f0faf4;
            border: 1px solid #b2d8c0;
            color: #1a6b3c;
        }

        /* Fade in */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-wrapper { animation: fadeUp 0.5s ease both; }
    </style>
</head>

<body>

<div class="login-wrapper">

    {{-- Brand Header --}}
    <div class="brand-header">
        <div class="brand-icon">
            <i class="fas fa-gavel"></i>
        </div>
        <h1 class="brand-name">LAPAU ANCAK</h1>
        <p class="brand-sub">Sistem Lelang Aset Negara</p>
    </div>

    {{-- Card --}}
    <div class="login-card">

        {{-- Accent line --}}
        <div class="card-accent"></div>

        <div class="card-body-inner">

            {{-- Heading --}}
            <div class="card-heading">
                <h2><i class="fas fa-sign-in-alt mr-2" style="color: #f6c90e; font-size: 1rem;"></i>Masuk ke Sistem</h2>
                <p>Gunakan akun yang telah terdaftar</p>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <label class="form-label">Email / Username</label>
                <div class="input-wrap">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Email atau username"
                        class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        required autofocus>
                </div>
                @error('email')
                <p class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                @enderror

                {{-- Password --}}
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password"
                           name="password"
                           id="passInput"
                           placeholder="••••••••"
                           class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           required>
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="fas fa-eye" id="passEye"></i>
                    </button>
                </div>
                @error('password')
                <p class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                @enderror
<!-- 
                {{-- Remember + Forgot --}}
                <div class="form-row-inline">
                    <label class="remember-label">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div> -->

                {{-- Submit --}}
                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>

            </form>

        </div>

        <div class="card-divider" style="margin: 0 36px;"></div>

        <div class="card-footer-inner">
            <p>
                <i class="fas fa-shield-alt"></i>
                Akses terbatas untuk pengguna terdaftar
            </p>
        </div>

    </div>

    {{-- Page footer --}}
    <div class="page-footer">
        <p>
            <i class="fas fa-gavel" style="color: #f6c90e; margin-right: 5px; font-size: 0.7rem;"></i>
            &copy; {{ date('Y') }} Lapau Ancak — Diamante
        </p>
    </div>

</div>

<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script>
    function togglePass() {
        const input = document.getElementById('passInput');
        const eye   = document.getElementById('passEye');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

</body>
</html>