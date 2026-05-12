<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIMDIS - Sistem Informasi Disiplin Siswa SMK 4 LPPM RI Padalarang">
    <title>SIMDIS - Sistem Informasi Disiplin Siswa</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet">

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
        :root {
            --navy: #0D2D6B;
            --navy-mid: #163580;
            --navy-dark: #091E4A;
            --gold: #F5B800;
            --gold-dark: #C99800;
            --gold-light: #f4f4f3;
            --gold-muted: rgba(245, 184, 0, 0.15);
            --gold-border: rgba(245, 184, 0, 0.4);
            --text: #0D2D6B;
            --muted: #4A5E8A;
            --bg: #F0F4FB;
            --white: #ffffff;
        }
 
        html {
            scroll-behavior: smooth;
        }
 
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding-top: 64px;
        }
 
        /* ─── NAVBAR ─── */
        nav {
            background: var(--bg);
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(13,45,107,.12);
        }
 
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
 
        .logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            flex-shrink: 0;
        }
 
        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }
 
        .logo-name {
            font-family: 'Lora', serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: 0.3px;
        }
 
        .logo-sub {
            font-size: 9px;
            color: var(--gold-dark);
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
 
        /* Hamburger */
        .hamburger {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 6px;
            border-radius: 6px;
            transition: background .2s;
        }
 
        .hamburger:hover {
            background: rgba(13,45,107,.08);
        }
 
        .hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--navy);
            border-radius: 2px;
            transition: all 0.3s ease;
        }
 
        .hamburger.active span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
 
        .hamburger.active span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
 
        .hamburger.active span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }
 
        /* Mobile Menu Dropdown */
        .mobile-menu {
            display: none;
            background: var(--navy-mid);
            border-top: 1px solid rgba(255,255,255,.08);
            padding: 1rem 2rem;
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            z-index: 99;
            flex-direction: column;
            gap: 4px;
        }
 
        .mobile-menu.open {
            display: flex;
        }
 
        .mobile-menu a {
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 14px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color .2s;
        }
 
        .mobile-menu a:last-child {
            border-bottom: none;
        }
 
        .mobile-menu a:hover {
            color: var(--gold);
        }
 
        /* ─── HERO ─── */
        .hero {
            background: var(--navy);
            padding: 5rem 2rem 4.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
 
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(245,184,0,.12) 0%, transparent 70%);
            pointer-events: none;
        }
 
        .hero::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), #FFD94D, var(--gold));
            opacity: 0.85;
        }
 
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245,184,0,.15);
            border: 1px solid rgba(245,184,0,.4);
            border-radius: 999px;
            padding: 5px 14px;
            margin-bottom: 1.5rem;
        }
 
        .hero-badge span {
            font-size: 11px;
            color: #FFD94D;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 600;
        }
 
        .hero-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
            animation: pulse 2s infinite;
        }
 
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }
 
        .hero h1 {
            font-family: 'Lora', serif;
            font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin-bottom: 0.8rem;
        }
 
        .hero h1 span {
            color: var(--gold);
        }
 
        .hero-sub {
            font-size: clamp(0.9rem, 2.5vw, 1.05rem);
            color: rgba(255, 255, 255, 0.6);
            max-width: 520px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }
 
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
 
        .btn-primary {
            background: var(--gold);
            color: var(--navy-dark);
            border: none;
            padding: 13px 28px;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
 
        .btn-primary:hover {
            background: var(--gold-dark);
            transform: translateY(-1px);
            color: var(--navy-dark);
        }
 
        .btn-outline {
            background: transparent;
            color: white;
            border: 1.5px solid rgba(255,255,255,.35);
            padding: 13px 28px;
            border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
 
        .btn-outline:hover {
            border-color: rgba(255,255,255,.7);
            background: rgba(255,255,255,.06);
            color: white;
        }
 
        /* ─── ABOUT SECTION ─── */
        .about-section {
            padding: 3.5rem 2rem;
            max-width: 860px;
            margin: 0 auto;
        }
 
        .section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gold-dark);
            margin-bottom: 0.5rem;
        }
 
        .about-section h2 {
            font-family: 'Lora', serif;
            font-size: clamp(1.4rem, 4vw, 1.9rem);
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 1rem;
            line-height: 1.3;
        }
 
        .about-text {
            font-size: 15px;
            line-height: 1.8;
            color: var(--muted);
            max-width: 640px;
        }
 
        .about-accent {
            display: inline-block;
            border-left: 3px solid var(--gold);
            padding-left: 14px;
            margin-top: 1.5rem;
            border-radius: 0;
        }
 
        .about-accent p {
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.7;
            font-style: italic;
        }
 
        /* ─── TUJUAN SECTION ─── */
        .tujuan-wrapper {
            padding: 0 2rem 3rem;
        }
 
        .tujuan-section {
            background: var(--navy);
            border-radius: 16px;
            padding: 2.5rem;
            max-width: 860px;
            margin: 0 auto;
        }
 
        .tujuan-section .section-label {
            color: var(--gold);
        }
 
        .tujuan-section h2 {
            font-family: 'Lora', serif;
            font-size: clamp(1.4rem, 4vw, 1.9rem);
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
 
        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }
 
        .goal-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.2rem 1.25rem;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            transition: background .2s, border-color .2s;
        }
 
        .goal-card:hover {
            background: rgba(255,255,255,.1);
            border-color: rgba(245,184,0,.5);
        }
 
        .goal-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(245, 184, 0, 0.2);
            border: 1px solid rgba(245, 184, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 12px;
            font-weight: 700;
            color: var(--gold);
        }
 
        .goal-text {
            font-size: 13.5px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.75);
        }
 
        /* ─── FOOTER ─── */
        footer {
            background: var(--navy);
            padding: 1.5rem 2rem;
            text-align: center;
            margin-top: 1rem;
        }
 
        footer p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.35);
            letter-spacing: 0.3px;
        }
 
        /* ─── RESPONSIVE ─── */
        @media (max-width: 600px) {
            .hero {
                padding: 3.5rem 1.25rem 3rem;
            }
 
            .about-section,
            .tujuan-wrapper {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
 
            .tujuan-section {
                padding: 1.75rem 1.25rem;
            }
 
            .goals-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    {{-- ─── NAVBAR ─── --}}
    <nav>
        <a href="{{ url('/') }}" class="logo-wrap">
            <img src="{{ asset('images/logo_simdis.png') }}" alt="Logo SIMDIS" class="logo-img">
            <div class="logo-text">
                <span class="logo-name">SIMDIS</span>
                <span class="logo-sub">SMK 4 LPPM RI</span>
            </div>
        </a>

        <button class="hamburger" id="hamburgerBtn" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobileMenu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    {{-- Mobile Dropdown Menu --}}
    <div class="mobile-menu" id="mobileMenu" role="navigation" aria-label="Menu mobile">
        <a href="#tentang"><i class="ti ti-info-circle" aria-hidden="true"></i> Tentang SIMDIS</a>
        <a href="#tujuan"><i class="ti ti-target" aria-hidden="true"></i> Tujuan</a>
        <a href="{{ route('login') }}"><i class="ti ti-login" aria-hidden="true"></i> Masuk Sistem</a>
    </div>

    {{-- ─── HERO ─── --}}
    <section class="hero" aria-labelledby="heroHeading">
        <h1 id="heroHeading">Selamat datang di <span>SIMDIS</span></h1>
        <p class="hero-sub">Sistem Informasi Disiplin Siswa SMK 4 LPPM RI Padalarang</p>

        <div class="btn-group">
            <a href="{{ route('login') }}" class="btn-primary">
                <i class="ti ti-login" aria-hidden="true" style="font-size:16px"></i>
                Masuk Sistem
            </a>
            <a href="#tentang" class="btn-outline">
                <i class="ti ti-info-circle" aria-hidden="true" style="font-size:16px"></i>
                Pelajari Lebih Lanjut
            </a>
        </div>
    </section>

    {{-- ─── TENTANG SIMDIS ─── --}}
    <section class="about-section" id="tentang" aria-labelledby="aboutHeading">
        <p class="section-label">Tentang</p>
        <h2 id="aboutHeading">Tentang SIMDIS</h2>
        <p class="about-text">
            SIMDIS (Sistem Informasi Disiplin Siswa) merupakan inovasi digital dari SMK 4 LPPM RI Padalarang
            yang dirancang untuk mendukung pembinaan karakter siswa melalui pengawasan pelanggaran secara terintegrasi.
        </p>
        <div class="about-accent">
            <p>Platform digital terpadu untuk monitoring dan pembinaan kedisiplinan siswa secara real-time.</p>
        </div>
    </section>

    {{-- ─── TUJUAN SIMDIS ─── --}}
<div class="tujuan-wrapper">
    <div class="tujuan-section" id="tujuan" aria-labelledby="tujuanHeading">
        <p class="section-label">Tujuan</p>
        <h2 id="tujuanHeading">Tujuan SIMDIS</h2>

        <div class="goals-grid">

            <div class="goal-card">
                <div class="goal-num" aria-hidden="true">1</div>
                <p class="goal-text">
                    Membantu sekolah dalam monitoring kedisiplinan siswa.
                </p>
            </div>

            <div class="goal-card">
                <div class="goal-num" aria-hidden="true">2</div>
                <p class="goal-text">
                    Menyediakan sistem peringatan dini otomatis untuk tindakan cepat.
                </p>
            </div>

            <div class="goal-card">
                <div class="goal-num" aria-hidden="true">3</div>
                <p class="goal-text">
                    Meningkatkan komunikasi antara guru BK, wali kelas, dan orang tua.
                </p>
            </div>

            <div class="goal-card">
                <div class="goal-num" aria-hidden="true">4</div>
                <p class="goal-text">
                    Menjadi dasar data pembinaan karakter siswa secara digital.
                </p>
            </div>

            <div class="goal-card">
                <div class="goal-num" aria-hidden="true">5</div>
                <p class="goal-text">
                    Mempermudah pencatatan dan pengelolaan data pelanggaran siswa secara terpusat.
                </p>
            </div>

            <div class="goal-card">
                <div class="goal-num" aria-hidden="true">6</div>
                <p class="goal-text">
                    Meningkatkan efisiensi dan transparansi proses pembinaan disiplin di lingkungan sekolah.
                </p>
            </div>

        </div>
    </div>
</div>

    {{-- ─── FOOTER ─── --}}
    <footer>
        <p>© {{ date('Y') }} SIMDIS &mdash; SMK 4 LPPM RI Padalarang. Hak cipta dilindungi.</p>
    </footer>

    {{-- ─── SCRIPT ─── --}}
    <script>
        const btn = document.getElementById('hamburgerBtn');
        const menu = document.getElementById('mobileMenu');

        btn.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            btn.classList.toggle('active', isOpen);
            btn.setAttribute('aria-expanded', isOpen);
        });

        // Tutup menu saat link diklik
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.remove('open');
                btn.classList.remove('active');
                btn.setAttribute('aria-expanded', false);
            });
        });
    </script>

</body>
</html>
