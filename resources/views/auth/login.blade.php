    <link rel="icon" type="image/svg+xml" 
    href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>
        <text y='.9em' font-size='90'>⚖️</text></svg>">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1a6b3c;
            --primary-light: #2d9e5f;
            --accent: #f6c90e;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f4526;
            background: radial-gradient(circle at top right, #1a6b3c, #0f4526);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Background Blobs */
        .blob {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(45, 158, 95, 0.2) 0%, rgba(26, 107, 60, 0.1) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: 0;
            animation: float 20s infinite alternate;
        }

        @keyframes float {
            from { transform: translate(-10%, -10%) rotate(0deg); }
            to { transform: translate(10%, 10%) rotate(360deg); }
        }

        .main-container {
            display: flex;
            width: 1000px;
            height: 600px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            position: relative;
            z-index: 10;
            animation: scaleIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Left Side: Illustration */
        .visual-side {
            flex: 1.1;
            background: linear-gradient(135deg, rgba(26, 107, 60, 0.9), rgba(15, 69, 38, 0.95));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
            position: relative;
            pointer-events: none;
        }

        .visual-side img {
            width: 80%;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.3));
            animation: softBounce 4s ease-in-out infinite;
        }

        @keyframes softBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Right Side: Form */
        .form-side {
            flex: 0.9;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.4);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .logo-box {
            width: 45px;
            height: 45px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 1.2rem;
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary);
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .welcome-text h2 {
            font-weight: 700;
            font-size: 1.75rem;
            color: #1e293b;
            margin: 0;
        }

        .welcome-text p {
            color: #64748b;
            font-size: 0.75rem;
            margin: 8px 0 32px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .input-field {
            position: relative;
        }

        .input-field i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s;
        }

        .input-field input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(255, 255, 255, 0.5);
            border: 2px solid transparent;
            border-radius: 14px;
            outline: none;
            transition: all 0.2s;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .input-field input:focus {
            background: white;
            border-color: var(--primary-light);
            box-shadow: 0 10px 15px -3px rgba(26, 107, 60, 0.1);
        }

        .input-field input:focus + i {
            color: var(--primary-light);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(26, 107, 60, 0.4);
        }

        .error-msg {
            color: #e11d48;
            font-size: 0.8rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .toggle-pass {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .main-container { width: 90%; height: auto; flex-direction: column; }
            .visual-side { display: none; }
            .form-side { padding: 40px 24px; }
        }
    </style>
</head>

<body>
    <div class="blob" style="top: -100px; left: -100px;"></div>
    <div class="blob" style="bottom: -100px; right: -100px; animation-delay: -5s;"></div>

    <div class="main-container">
        <div class="visual-side">
            <img src="{{ asset('template/img/logoBPA.png') }}" alt="Login Illustration">
            <div style="text-align: center; margin-top: 20px;">
                <h3 style="font-weight: 800; font-size: 1.5rem; margin-bottom: 10px;">Portal Administrasi</h3>
                <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">Kelola lelang barang rampasan negara dengan aman, transparan, dan terintegrasi dalam satu platform.</p>
            </div>
        </div>

        <div class="form-side">
            <div class="brand-logo">
                <div class="logo-box"><i class="fas fa-gavel"></i></div>
                <span class="brand-title">Lapau Ancak</span>
            </div>

            <div class="welcome-text">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk menggunakan akun kredensial Anda.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label>Email atau Username</label>
                    <div class="input-field">
                        <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan email/username">
                        <i class="fas fa-envelope"></i>
                    </div>
                    @error('email')
                        <span class="error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label>Kata Sandi</label>
                    <div class="input-field">
                        <input type="password" name="password" id="passInput" required placeholder="••••••••">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="toggle-pass" onclick="togglePass()">
                            <i class="fas fa-eye" id="passEye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Masuk Ke Sistem</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

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
