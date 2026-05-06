<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapau Ancak</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚖️</text></svg>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes ping {
            75%, 100% { transform: scale(2); opacity: 0; }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">

    {{-- NAVBAR --}}
    <nav class="bg-blue-900 text-white px-4 py-3 sticky top-0 z-40 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">

            <a href="{{ route('public.index') }}" class="flex items-center gap-2 font-bold text-lg">
                ⚖️ <span>Lapau Ancak</span>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('public.index') }}"
                    class="text-sm text-blue-200 hover:text-white transition">
                    Beranda
                </a>
                <a href="#"
                    class="text-sm text-blue-200 hover:text-white transition">
                    Lelang
                </a>

                {{-- Tombol Login Admin --}}
                <a href="{{ route('login') }}"
                    class="bg-yellow-400 hover:bg-yellow-300 text-blue-900 font-bold px-3 py-1.5 rounded-lg text-sm transition"
                    title="Login Admin">
                    🔐
                </a>
            </div>

        </div>
    </nav>

    {{-- CONTENT --}}
    @yield('content')

    {{-- FOOTER --}}
    <footer class="bg-blue-900 text-white py-8 mt-10">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="text-xl">⚖️</span>
                <span class="font-bold">Lapau Ancak</span>
            </div>
            <p class="text-blue-300 text-sm">Platform Resmi Lelang Barang Rampasan Negara</p>
            <p class="text-blue-400 text-xs mt-3">&copy; 2026 Diamante. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>