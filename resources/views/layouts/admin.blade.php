<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapau Ancak</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚖️</text></svg>">

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    @include('components.sidebar')

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            @include('components.topbar')

            <div class="container-fluid">
                @yield('content')
            </div>

        </div>
    </div>

</div>

<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>

@include('components.footer')

{{-- SCROLL BUTTON --}}
<button id="scrollBtn" onclick="toggleScroll()" 
    style="
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #4e73df;
        color: white;
        border: none;
        font-size: 18px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 9999;
        transition: background 0.2s;">
    <i id="scrollIcon" class="fas fa-arrow-up"></i>
</button>

<script>
const scrollBtn = document.getElementById('scrollBtn');
const scrollIcon = document.getElementById('scrollIcon');

window.addEventListener('scroll', function () {
    if (window.scrollY > 200) {
        scrollBtn.style.display = 'flex';
    } else {
        scrollBtn.style.display = 'none';
    }

    // Ganti ikon: jika sudah di bawah (dekat bottom) tampilkan panah atas, sebaliknya bawah
    const atBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 100;
    scrollIcon.className = atBottom ? 'fas fa-arrow-up' : 'fas fa-arrow-up';

    // Ubah arah berdasarkan posisi scroll
    if (window.scrollY < document.body.offsetHeight / 2) {
        scrollIcon.className = 'fas fa-arrow-down';
    } else {
        scrollIcon.className = 'fas fa-arrow-up';
    }
});

function toggleScroll() {
    const halfway = document.body.offsetHeight / 2;

    if (window.scrollY < halfway) {
        // Scroll ke bawah
        window.scrollTo({ top: document.body.offsetHeight, behavior: 'smooth' });
    } else {
        // Scroll ke atas
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
</script>

</body>
</html>