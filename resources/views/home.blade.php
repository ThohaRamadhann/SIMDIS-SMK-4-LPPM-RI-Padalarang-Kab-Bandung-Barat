<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIMDIS - Sistem Informasi Disiplin Siswa SMK 4 LPPM RI Padalarang">
    <title>SIMDIS - Sistem Informasi Disiplin Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_simdis.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Lora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --navy: #0D2D6B;
            --navy-mid: #163580;
            --navy-dark: #091E4A;
            --navy-light: #1E3E8A;
            --gold: #F5B800;
            --gold-dark: #C99800;
            --gold-muted: rgba(245,184,0,.15);
            --gold-border: rgba(245,184,0,.4);
            --text: #0D2D6B;
            --muted: #4A5E8A;
            --bg: #F0F4FB;
            --white: #ffffff;
            --card-bg: #ffffff;
            --divider: rgba(13,45,107,.08);
        }

        html { scroll-behavior: smooth; }

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
            top: 0; left: 0; right: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(13,45,107,.1);
        }

        .logo-wrap { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .logo-img { width: 42px; height: 42px; object-fit: contain; }
        .logo-text { display: flex; flex-direction: column; line-height: 1.1; }
        .logo-name { font-family: 'Lora', serif; font-size: 17px; font-weight: 700; color: var(--navy); }
        .logo-sub { font-size: 9px; color: var(--gold-dark); letter-spacing: 1.2px; text-transform: uppercase; }

        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a {
            font-size: 13.5px;
            color: var(--muted);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 7px;
            font-weight: 500;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .nav-links a:hover { color: var(--navy); background: rgba(13,45,107,.06); }
        .nav-links .nav-cta {
            background: var(--navy);
            color: white;
            padding: 7px 18px;
            margin-left: 4px;
        }
        .nav-links .nav-cta:hover { background: var(--navy-light); color: white; }

        .hamburger {
            display: none;
            background: none; border: none; cursor: pointer;
            flex-direction: column; gap: 5px; padding: 6px;
            border-radius: 6px; transition: background .2s;
        }
        .hamburger:hover { background: rgba(13,45,107,.08); }
        .hamburger span { display: block; width: 24px; height: 2px; background: var(--navy); border-radius: 2px; transition: all .3s ease; }
        .hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .mobile-menu {
            display: none;
            background: var(--navy-mid);
            border-top: 1px solid rgba(255,255,255,.08);
            padding: 1rem 2rem;
            position: fixed;
            top: 64px; left: 0; right: 0;
            z-index: 99;
            flex-direction: column;
            gap: 4px;
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a {
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 14px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
            display: flex; align-items: center; gap: 8px;
            transition: color .2s;
        }
        .mobile-menu a:last-child { border-bottom: none; }
        .mobile-menu a:hover { color: var(--gold); }

        /* ─── HERO ─── */
        .hero {
            background: var(--navy);
            padding: 5.5rem 2rem 5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 90% 60% at 50% -10%, rgba(245,184,0,.13) 0%, transparent 65%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), #FFD94D, var(--gold));
            opacity: .85;
        }
        .hero-deco {
            position: absolute;
            border-radius: 50%;
            opacity: .04;
            pointer-events: none;
        }
        .hero-deco-1 { width: 500px; height: 500px; background: var(--gold); top: -200px; right: -120px; }
        .hero-deco-2 { width: 300px; height: 300px; background: white; bottom: -100px; left: -60px; }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(245,184,0,.15);
            border: 1px solid rgba(245,184,0,.4);
            border-radius: 999px;
            padding: 5px 16px;
            margin-bottom: 1.5rem;
        }
        .hero-badge span { font-size: 11px; color: #FFD94D; letter-spacing: .8px; text-transform: uppercase; font-weight: 600; }
        .hero-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--gold);
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.5; transform:scale(.8); } }

        .hero h1 {
            font-family: 'Lora', serif;
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin-bottom: .8rem;
        }
        .hero h1 span { color: var(--gold); }

        .hero-sub {
            font-size: clamp(.9rem, 2.5vw, 1.05rem);
            color: rgba(255,255,255,.6);
            max-width: 520px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .btn-group { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        .btn-primary {
            background: var(--gold); color: var(--navy-dark);
            border: none; padding: 13px 28px; border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all .2s;
            display: inline-flex; align-items: center; gap: 7px;
            text-decoration: none;
        }
        .btn-primary:hover { background: var(--gold-dark); transform: translateY(-1px); color: var(--navy-dark); }

        .btn-outline {
            background: transparent; color: white;
            border: 1.5px solid rgba(255,255,255,.35);
            padding: 13px 28px; border-radius: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 500; cursor: pointer;
            transition: all .2s;
            display: inline-flex; align-items: center; gap: 7px;
            text-decoration: none;
        }
        .btn-outline:hover { border-color: rgba(255,255,255,.7); background: rgba(255,255,255,.06); color: white; }

        /* Hero Stats */
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-top: 3.5rem;
            border-top: 1px solid rgba(255,255,255,.1);
            padding-top: 2rem;
            flex-wrap: wrap;
        }
        .stat-item {
            flex: 1;
            min-width: 120px;
            max-width: 200px;
            padding: 0.5rem 1.5rem;
            border-right: 1px solid rgba(255,255,255,.1);
            text-align: center;
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-family: 'Lora', serif;
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--gold);
            display: block;
        }
        .stat-label { font-size: 11.5px; color: rgba(255,255,255,.5); letter-spacing: .4px; margin-top: 3px; }

        /* ─── FEATURES STRIP ─── */
        .features-strip {
            background: var(--white);
            padding: 2.5rem 2rem;
            border-bottom: 1px solid var(--divider);
        }
        .features-inner {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
        }
        .feat-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .feat-icon {
            width: 40px; height: 40px;
            background: rgba(13,45,107,.07);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            color: var(--navy);
            font-size: 20px;
        }
        .feat-label { font-size: 13px; font-weight: 600; color: var(--navy); margin-bottom: 2px; }
        .feat-desc { font-size: 12px; color: var(--muted); line-height: 1.5; }

        /* ─── ABOUT SECTION ─── */
        .about-section {
            padding: 4rem 2rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--gold-dark);
            margin-bottom: .5rem;
        }
        .section-title {
            font-family: 'Lora', serif;
            font-size: clamp(1.4rem, 4vw, 1.9rem);
            font-weight: 700; color: var(--navy);
            margin-bottom: 1rem; line-height: 1.3;
        }
        .about-text {
            font-size: 15px; line-height: 1.8; color: var(--muted);
        }
        .about-accent {
            display: block;
            border-left: 3px solid var(--gold);
            padding: 10px 14px;
            margin-top: 1.5rem;
            background: rgba(245,184,0,.06);
            border-radius: 0 6px 6px 0;
        }
        .about-accent p { font-size: 13.5px; color: var(--muted); line-height: 1.7; font-style: italic; }

        /* School Info Card */
        .school-card {
            background: var(--white);
            border: 1px solid var(--divider);
            border-radius: 14px;
            overflow: hidden;
        }
        .school-card-header {
            background: var(--navy);
            padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 10px;
        }
        .school-card-header .ti { font-size: 18px; color: var(--gold); }
        .school-card-header span { font-size: 13px; font-weight: 600; color: white; letter-spacing: .3px; }
        .school-info-list { padding: 0; list-style: none; }
        .school-info-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 11px 1.5rem;
            border-bottom: 1px solid var(--divider);
            font-size: 13px;
        }
        .school-info-item:last-child { border-bottom: none; }
        .info-key { color: var(--muted); flex-shrink: 0; width: 100px; }
        .info-val { color: var(--navy); font-weight: 600; flex: 1; }
        .info-badge {
            display: inline-block;
            background: rgba(245,184,0,.15);
            border: 1px solid rgba(245,184,0,.4);
            color: var(--gold-dark);
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        /* ─── TUJUAN SECTION ─── */
        .tujuan-wrapper { padding: 0 2rem 4rem; }
        .tujuan-section {
            background: var(--navy);
            border-radius: 18px;
            padding: 3rem 2.5rem;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .tujuan-section::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            border: 1px solid rgba(245,184,0,.15);
            pointer-events: none;
        }
        .tujuan-section::after {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 360px; height: 360px;
            border-radius: 50%;
            border: 1px solid rgba(245,184,0,.08);
            pointer-events: none;
        }
        .tujuan-section .section-label { color: var(--gold); }
        .tujuan-section .section-title { color: white; margin-bottom: 1.75rem; }

        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }
        .goal-card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px;
            padding: 1.25rem 1.25rem;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            transition: background .2s, border-color .2s, transform .2s;
        }
        .goal-card:hover { background: rgba(255,255,255,.1); border-color: rgba(245,184,0,.4); transform: translateY(-2px); }
        .goal-num {
            width: 30px; height: 30px; border-radius: 50%;
            background: rgba(245,184,0,.18);
            border: 1px solid rgba(245,184,0,.5);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 12px; font-weight: 700; color: var(--gold);
        }
        .goal-text { font-size: 13.5px; line-height: 1.65; color: rgba(255,255,255,.75); }

        /* ─── CONTACT SECTION ─── */
        .contact-wrapper { padding: 0 2rem 4rem; }
        .contact-section { max-width: 900px; margin: 0 auto; }
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-top: 1.5rem;
        }
        .contact-card {
            background: var(--white);
            border: 1px solid var(--divider);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: border-color .2s, transform .2s;
            text-decoration: none;
            color: inherit;
        }
        .contact-card:hover { border-color: rgba(13,45,107,.25); transform: translateY(-2px); }
        .contact-card-icon {
            width: 42px; height: 42px;
            background: rgba(13,45,107,.07);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: var(--navy);
            flex-shrink: 0;
        }
        .contact-card-label { font-size: 11.5px; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
        .contact-card-val { font-size: 14px; font-weight: 600; color: var(--navy); line-height: 1.4; }

        /* ─── FOOTER ─── */
        footer {
            background: var(--navy);
            padding: 2.5rem 2rem 1.5rem;
        }
        .footer-inner { max-width: 900px; margin: 0 auto; }
        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-wrap: wrap;
        }
        .footer-brand { display: flex; align-items: center; gap: 10px; }
        .footer-brand-text .logo-name { color: white; }
        .footer-brand-text .logo-sub { color: rgba(245,184,0,.7); }
        .footer-desc { font-size: 13px; color: rgba(255,255,255,.45); margin-top: 10px; max-width: 280px; line-height: 1.65; }
        .footer-links-title { font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 12px; font-weight: 600; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .footer-links a { font-size: 13.5px; color: rgba(255,255,255,.55); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--gold); }
        .footer-bottom {
            padding-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .footer-copy { font-size: 12px; color: rgba(255,255,255,.3); }
        .footer-npsn { font-size: 12px; color: rgba(255,255,255,.25); }

        /* ─── RESPONSIVE ─── */
        @media (min-width: 769px) { .hamburger { display: none !important; } }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hamburger { display: flex; }
            .two-col { grid-template-columns: 1fr; gap: 2rem; }
            .stat-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,.1); max-width: none; }
            .stat-item:last-child { border-bottom: none; }
        }
        @media (max-width: 600px) {
            .hero { padding: 3.5rem 1.25rem 3rem; }
            .about-section, .tujuan-wrapper, .contact-wrapper { padding-left: 1.25rem; padding-right: 1.25rem; }
            .tujuan-section { padding: 2rem 1.25rem; }
            .goals-grid { grid-template-columns: 1fr; }
            .features-inner { grid-template-columns: 1fr 1fr; }
            .footer-top { flex-direction: column; }
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

        <div class="nav-links">
            <a href="#tentang"><i class="ti ti-info-circle" aria-hidden="true"></i> Tentang</a>
            <a href="#tujuan"><i class="ti ti-target" aria-hidden="true"></i> Tujuan</a>
            <a href="#kontak"><i class="ti ti-map-pin" aria-hidden="true"></i> Kontak</a>
            <a href="{{ route('login') }}" class="nav-cta"><i class="ti ti-login" aria-hidden="true"></i> Masuk Sistem</a>
        </div>

        <button class="hamburger" id="hamburgerBtn" aria-label="Buka menu" aria-expanded="false" aria-controls="mobileMenu">
            <span></span><span></span><span></span>
        </button>
    </nav>

    {{-- Mobile Menu --}}
    <div class="mobile-menu" id="mobileMenu" role="navigation" aria-label="Menu mobile">
        <a href="#tentang"><i class="ti ti-info-circle" aria-hidden="true"></i> Tentang SIMDIS</a>
        <a href="#tujuan"><i class="ti ti-target" aria-hidden="true"></i> Tujuan</a>
        <a href="#kontak"><i class="ti ti-map-pin" aria-hidden="true"></i> Kontak & Lokasi</a>
        <a href="{{ route('login') }}"><i class="ti ti-login" aria-hidden="true"></i> Masuk Sistem</a>
    </div>

    {{-- ─── HERO ─── --}}
    <section class="hero" aria-labelledby="heroHeading">
        <div class="hero-deco hero-deco-1"></div>
        <div class="hero-deco hero-deco-2"></div>

        <div class="hero-badge" aria-label="Platform Aktif">
            <div class="hero-badge-dot"></div>
            <span>Platform Aktif &mdash; SMK 4 LPPM RI Padalarang</span>
        </div>

        <h1 id="heroHeading">Selamat Datang di <span>SIMDIS</span></h1>
        <p class="hero-sub">Sistem Informasi Disiplin Siswa — platform digital terpadu untuk monitoring, pembinaan, dan pengelolaan kedisiplinan siswa secara real-time.</p>

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

        <div class="hero-stats" aria-label="Statistik sistem">
            <div class="stat-item">
                <span class="stat-num">6</span>
                <span class="stat-label">Tujuan Utama</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">Real-Time</span>
                <span class="stat-label">Monitoring Aktif</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">4</span>
                <span class="stat-label">Peran Pengguna</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">Digital</span>
                <span class="stat-label">Pencatatan Terpusat</span>
            </div>
        </div>
    </section>

    {{-- ─── FEATURES STRIP ─── --}}
    <div class="features-strip" aria-label="Fitur utama">
        <div class="features-inner">
            <div class="feat-item">
                <div class="feat-icon"><i class="ti ti-shield-check" aria-hidden="true"></i></div>
                <div>
                    <div class="feat-label">Monitoring Disiplin</div>
                    <div class="feat-desc">Pantau pelanggaran siswa secara terpusat dan real-time</div>
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="ti ti-bell-ringing" aria-hidden="true"></i></div>
                <div>
                    <div class="feat-label">Peringatan Otomatis</div>
                    <div class="feat-desc">Notifikasi dini untuk tindakan cepat dan tepat</div>
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="ti ti-users" aria-hidden="true"></i></div>
                <div>
                    <div class="feat-label">Kolaborasi Multi-Peran</div>
                    <div class="feat-desc">Guru BK, wali kelas, dan orang tua terhubung</div>
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="ti ti-chart-bar" aria-hidden="true"></i></div>
                <div>
                    <div class="feat-label">Laporan & Analitik</div>
                    <div class="feat-desc">Data pembinaan karakter berbasis digital</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── TENTANG & INFO SEKOLAH ─── --}}
    <section class="about-section" id="tentang" aria-labelledby="aboutHeading">
        <div class="two-col">
            <div>
                <p class="section-label">Tentang</p>
                <h2 class="section-title" id="aboutHeading">Tentang SIMDIS</h2>
                <p class="about-text">
                    SIMDIS (Sistem Informasi Disiplin Siswa) merupakan inovasi digital dari SMK 4 LPPM RI Padalarang yang dirancang untuk mendukung pembinaan karakter siswa melalui pengawasan pelanggaran secara terintegrasi dan terstruktur.
                </p>
                <p class="about-text" style="margin-top:1rem">
                    Sistem ini menghubungkan guru BK, wali kelas, dan orang tua dalam satu platform yang memudahkan komunikasi dan pengambilan keputusan pembinaan secara transparan.
                </p>
                <div class="about-accent">
                    <p>Platform digital terpadu untuk monitoring dan pembinaan kedisiplinan siswa secara real-time di lingkungan SMK 4 LPPM RI Padalarang.</p>
                </div>
            </div>

            <div>
                <div class="school-card">
                    <div class="school-card-header">
                        <i class="ti ti-school" aria-hidden="true"></i>
                        <span>Profil Sekolah</span>
                    </div>
                    <ul class="school-info-list">
                        <li class="school-info-item">
                            <span class="info-key">Nama</span>
                            <span class="info-val">SMKS 4 LPPM RI</span>
                        </li>
                        <li class="school-info-item">
                            <span class="info-key">NPSN</span>
                            <span class="info-val">20267646</span>
                        </li>
                        <li class="school-info-item">
                            <span class="info-key">Status</span>
                            <span class="info-val"><span class="info-badge">SWASTA</span></span>
                        </li>
                        <li class="school-info-item">
                            <span class="info-key">Jenjang</span>
                            <span class="info-val">SMK — Pendidikan Menengah</span>
                        </li>
                        <li class="school-info-item">
                            <span class="info-key">Alamat</span>
                            <span class="info-val">Jl. Letkol GA Manulang No. 132, Padalarang</span>
                        </li>
                        <li class="school-info-item">
                            <span class="info-key">Wilayah</span>
                            <span class="info-val">Kec. Padalarang, Kab. Bandung Barat, Jawa Barat</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── TUJUAN ─── --}}
    <div class="tujuan-wrapper">
        <div class="tujuan-section" id="tujuan" aria-labelledby="tujuanHeading">
            <p class="section-label">Tujuan</p>
            <h2 class="section-title" id="tujuanHeading">Tujuan SIMDIS</h2>

            <div class="goals-grid">
                <div class="goal-card">
                    <div class="goal-num" aria-hidden="true">1</div>
                    <p class="goal-text">Membantu sekolah dalam monitoring kedisiplinan siswa secara menyeluruh dan akurat.</p>
                </div>
                <div class="goal-card">
                    <div class="goal-num" aria-hidden="true">2</div>
                    <p class="goal-text">Menyediakan sistem peringatan dini otomatis untuk tindakan cepat dan responsif.</p>
                </div>
                <div class="goal-card">
                    <div class="goal-num" aria-hidden="true">3</div>
                    <p class="goal-text">Meningkatkan komunikasi antara guru BK, wali kelas, dan orang tua siswa.</p>
                </div>
                <div class="goal-card">
                    <div class="goal-num" aria-hidden="true">4</div>
                    <p class="goal-text">Menjadi dasar data pembinaan karakter siswa secara digital dan terstruktur.</p>
                </div>
                <div class="goal-card">
                    <div class="goal-num" aria-hidden="true">5</div>
                    <p class="goal-text">Mempermudah pencatatan dan pengelolaan data pelanggaran siswa secara terpusat.</p>
                </div>
                <div class="goal-card">
                    <div class="goal-num" aria-hidden="true">6</div>
                    <p class="goal-text">Meningkatkan efisiensi dan transparansi proses pembinaan disiplin di lingkungan sekolah.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── KONTAK ─── --}}
    <div class="contact-wrapper">
        <section class="contact-section" id="kontak" aria-labelledby="kontakHeading">
            <p class="section-label">Kontak & Lokasi</p>
            <h2 class="section-title" id="kontakHeading">Hubungi Kami</h2>

            <div class="contact-grid">
                <a href="tel:+626810404" class="contact-card">
                    <div class="contact-card-icon"><i class="ti ti-phone" aria-hidden="true"></i></div>
                    <div>
                        <div class="contact-card-label">Telepon</div>
                        <div class="contact-card-val">(022) 6810404</div>
                    </div>
                </a>
                <a href="mailto:smk4lppmri@gmail.com" class="contact-card">
                    <div class="contact-card-icon"><i class="ti ti-mail" aria-hidden="true"></i></div>
                    <div>
                        <div class="contact-card-label">Email</div>
                        <div class="contact-card-val">smk4lppmri@gmail.com</div>
                    </div>
                </a>
                <a href="https://www.instagram.com/smk4lppmri/" target="_blank" rel="noopener noreferrer" class="contact-card">
                    <div class="contact-card-icon"><i class="ti ti-brand-instagram" aria-hidden="true"></i></div>
                    <div>
                        <div class="contact-card-label">Instagram</div>
                        <div class="contact-card-val">@smk4lppmri</div>
                    </div>
                </a>
                <div class="contact-card" style="cursor:default">
                    <div class="contact-card-icon"><i class="ti ti-map-pin" aria-hidden="true"></i></div>
                    <div>
                        <div class="contact-card-label">Alamat</div>
                        <div class="contact-card-val">Jl. Letkol GA Manulang No. 132, Padalarang, Kab. Bandung Barat</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ─── FOOTER ─── --}}
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand">
                        <img src="{{ asset('images/logo_simdis.png') }}" alt="Logo SIMDIS" style="width:36px;height:36px;object-fit:contain;filter:brightness(0) invert(1);opacity:.85">
                        <div class="footer-brand-text logo-text">
                            <span class="logo-name">SIMDIS</span>
                            <span class="logo-sub">SMK 4 LPPM RI</span>
                        </div>
                    </div>
                    <p class="footer-desc">Platform digital untuk monitoring dan pembinaan kedisiplinan siswa SMK 4 LPPM RI Padalarang.</p>
                </div>

                <div>
                    <div class="footer-links-title">Navigasi</div>
                    <ul class="footer-links">
                        <li><a href="#tentang">Tentang SIMDIS</a></li>
                        <li><a href="#tujuan">Tujuan</a></li>
                        <li><a href="#kontak">Kontak & Lokasi</a></li>
                        <li><a href="{{ route('login') }}">Masuk Sistem</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-links-title">Hubungi Kami</div>
                    <ul class="footer-links">
                        <li><a href="mailto:smk4lppmri@gmail.com"><i class="ti ti-mail" aria-hidden="true" style="margin-right:5px"></i>smk4lppmri@gmail.com</a></li>
                        <li><a href="tel:+626810404"><i class="ti ti-phone" aria-hidden="true" style="margin-right:5px"></i>(022) 6810404</a></li>
                        <li><a href="https://www.instagram.com/smk4lppmri/" target="_blank" rel="noopener noreferrer"><i class="ti ti-brand-instagram" aria-hidden="true" style="margin-right:5px"></i>@smk4lppmri</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-copy">© {{ date('Y') }} SIMDIS &mdash; SMK 4 LPPM RI Padalarang. Hak cipta dilindungi.</p>
                <p class="footer-npsn">NPSN: 20267646</p>
            </div>
        </div>
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