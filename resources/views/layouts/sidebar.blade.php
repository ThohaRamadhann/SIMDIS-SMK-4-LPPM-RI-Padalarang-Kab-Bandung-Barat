@php
    // Ambil data role user yang sedang login
    $user = Auth::user();
    $role = optional($user->role)->nama_role;
@endphp

<style>
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

    .simdis-sidebar {
        width: 16rem;
        min-height: 100vh;
        background: linear-gradient(180deg, var(--navy-dark) 0%, var(--navy) 60%, var(--navy-mid) 100%);
        color: var(--white);
        box-shadow: 4px 0 24px rgba(9, 30, 74, 0.35);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Subtle geometric background texture */
    .simdis-sidebar::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 20%, rgba(245, 184, 0, 0.06) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(245, 184, 0, 0.04) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }

    .simdis-sidebar>* {
        position: relative;
        z-index: 1;
    }

    /* ── Logo / Header ── */
    .simdis-brand {
        padding: 1.25rem 1.25rem 1rem;
        border-bottom: 1px solid var(--gold-border);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: rgba(9, 30, 74, 0.4);
    }

    .simdis-logo {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(245, 184, 0, 0.4);
    }

    .simdis-logo svg {
        width: 22px;
        height: 22px;
        fill: var(--navy-dark);
    }

    .simdis-brand-text h3 {
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: var(--white);
        line-height: 1.2;
        margin: 0;
    }

    .simdis-brand-text span {
        font-size: 0.7rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    /* ── Role badge ── */
    .simdis-role-badge {
        margin: 0.85rem 1.25rem 0;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--gold-muted);
        border: 1px solid var(--gold-border);
        border-radius: 20px;
        padding: 0.25rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--gold);
    }

    .simdis-role-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        background: var(--gold);
        border-radius: 50%;
        animation: simdis-pulse 2s ease-in-out infinite;
    }

    @keyframes simdis-pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(0.8);
        }
    }

    /* ── Nav ── */
    .simdis-nav {
        margin-top: 1rem;
        padding: 0 0.75rem;
        flex: 1;
    }

    .simdis-section-label {
        padding: 0.6rem 0.5rem 0.3rem;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(245, 184, 0, 0.55);
        margin-top: 0.5rem;
    }

    .simdis-divider {
        height: 1px;
        background: var(--gold-border);
        margin: 0.5rem 0;
        opacity: 0.5;
    }

    /* ── Nav link base ── */
    .simdis-nav-link {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        margin-bottom: 2px;
        position: relative;
        overflow: hidden;

        /* Smooth transition for all states */
        transition:
            color 0.22s ease,
            background 0.22s ease,
            box-shadow 0.22s ease,
            transform 0.18s ease;
    }

    .simdis-nav-link .simdis-nav-icon {
        width: 1.1rem;
        text-align: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: color 0.22s ease, transform 0.22s ease;
    }

    /* Left accent bar (hidden by default) */
    .simdis-nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 3px;
        background: var(--gold);
        border-radius: 0 3px 3px 0;
        opacity: 0;
        transform: scaleY(0.4);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    /* Hover state */
    .simdis-nav-link:hover {
        color: var(--white);
        background: rgba(245, 184, 0, 0.1);
        transform: translateX(3px);
    }

    .simdis-nav-link:hover .simdis-nav-icon {
        color: var(--gold);
        transform: scale(1.15);
    }

    /* Active state */
    .simdis-nav-link.active {
        color: var(--navy-dark);
        background: linear-gradient(90deg, var(--gold) 0%, var(--gold-dark) 100%);
        box-shadow: 0 4px 14px rgba(245, 184, 0, 0.35);
        font-weight: 700;
        transform: translateX(2px);
    }

    .simdis-nav-link.active::before {
        opacity: 0;
        /* bar hidden on active—gold bg is enough */
    }

    .simdis-nav-link.active .simdis-nav-icon {
        color: var(--navy-dark);
    }

    /* Ripple on click */
    .simdis-nav-link .simdis-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        width: 0;
        height: 0;
        transform: translate(-50%, -50%);
        pointer-events: none;
        animation: simdis-ripple-anim 0.45s ease-out forwards;
    }

    @keyframes simdis-ripple-anim {
        to {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }

    /* ── Footer ── */
    .simdis-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--gold-border);
        background: rgba(9, 30, 74, 0.4);
    }

    .simdis-logout {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 0.75rem;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        transition: color 0.2s ease, background 0.2s ease;
        cursor: pointer;
        background: transparent;
        border: none;
        width: 100%;
    }

    .simdis-logout:hover {
        color: #ff6b6b;
        background: rgba(255, 107, 107, 0.1);
    }
</style>

<aside class="simdis-sidebar">

    {{-- ── Brand / Logo ── --}}
    <div class="simdis-brand">

        <div class="simdis-logo">

            {{-- Icon disiplin siswa --}}
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 2L4 5v6c0 5 3.5 9.5 8 11 4.5-1.5 8-6 8-11V5l-8-3zm-1 12l-3-3 1.4-1.4L11 11.2l3.6-3.6L16 9l-5 5z" />
            </svg>

        </div>

        <div class="simdis-brand-text">
            <h3>SIMDIS</h3>
            <span>Sistem Informasi Disiplin Siswa</span>
        </div>

    </div>

    {{-- ── Role badge ── --}}
    <div class="simdis-role-badge">
        {{ str_replace('_', ' ', $role) }}
    </div>

    {{-- ── Navigation ── --}}
    <nav class="simdis-nav">

        {{-- General --}}
        <a href="{{ route('dashboard') }}" class="simdis-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt simdis-nav-icon"></i>
            Dashboard
        </a>

        <a href="{{ route('profile') }}" class="simdis-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle simdis-nav-icon"></i>
            Profile
        </a>

        <div class="simdis-divider"></div>

        {{-- Manajemen Data (non-admin) --}}
        @if ($role !== 'admin')
            <div class="simdis-section-label">Manajemen Data</div>
        @endif

        @if (in_array($role, ['guru_bk', 'wali_kelas', 'orang_tua']))
            <a href="{{ route('pelanggaran.index') }}"
                class="simdis-nav-link {{ request()->routeIs('pelanggaran.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle simdis-nav-icon"></i>
                {{-- Label berbeda untuk orang_tua agar lebih kontekstual --}}
                {{ $role === 'orang_tua' ? 'Pelanggaran Anak' : 'Data Pelanggaran' }}
            </a>
        @endif

        {{-- Administrasi Sistem (admin only) --}}
        @if ($role === 'admin')
            <div class="simdis-section-label">Administrasi Sistem</div>

            <a href="{{ route('users') }}" class="simdis-nav-link {{ request()->routeIs('users') ? 'active' : '' }}">
                <i class="fas fa-users-cog simdis-nav-icon"></i>
                Pengguna
            </a>

            <a href="{{ route('siswa') }}" class="simdis-nav-link {{ request()->routeIs('siswa') ? 'active' : '' }}">
                <i class="fas fa-user-graduate simdis-nav-icon"></i>
                Siswa
            </a>

            <a href="{{ route('wali-murid') }}"
                class="simdis-nav-link {{ request()->routeIs('wali-murid') ? 'active' : '' }}">
                <i class="fas fa-user-tie simdis-nav-icon"></i>
                Wali Murid
            </a>

            <a href="{{ route('wali-kelas') }}"
                class="simdis-nav-link {{ request()->routeIs('wali-kelas') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher simdis-nav-icon"></i>
                Wali Kelas
            </a>

            <a href="{{ route('kelas') }}" class="simdis-nav-link {{ request()->routeIs('kelas') ? 'active' : '' }}">
                <i class="fas fa-school simdis-nav-icon"></i>
                Kelas
            </a>
        @endif

    </nav>

    {{-- ── Footer / Logout ── --}}
    {{-- <div class="simdis-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="simdis-logout">
                <i class="fas fa-sign-out-alt"></i>
                Keluar
            </button>
        </form>
    </div> --}}
</aside>

{{-- Ripple effect on nav links --}}
<script>
    document.querySelectorAll('.simdis-nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('simdis-ripple');
            const rect = this.getBoundingClientRect();
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
        });
    });
</script>
