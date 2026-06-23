@php
    $user = Auth::user();
    $dashboardActive = request()->routeIs('dashboard', 'admin.dashboard', 'manager.dashboard');
    $usersActive = request()->routeIs('admin.users.*');
    $criteriaActive = request()->routeIs('admin.criteria.*');
    $influencersActive = request()->routeIs('admin.influencers.*');
    $sawActive = request()->routeIs('admin.saw.*', 'manager.saw.*');
    $sawRoute = $user->isAdmin() ? 'admin.saw.index' : 'manager.saw.index';
@endphp

<aside class="sidebar" aria-label="{{ __('Navigasi Utama') }}">
    <a class="brand" href="{{ route('dashboard') }}">
        <div class="brand-mark">
            <span style="color: #FFF;">SAW</span>
        </div>
        <span>
            <strong>{{ __('SPK Influencer') }}</strong>
            <span>{{ __('PT. Behaestex') }}</span>
        </span>
    </a>

    <nav class="nav" aria-label="{{ __('Menu') }}">
        <a href="{{ route('dashboard') }}" @class(['active' => $dashboardActive])>
            <span>{{ __('Dashboard') }}</span>
        </a>

        @if ($user->isAdmin() && Route::has('admin.users.index'))
            <a href="{{ route('admin.users.index') }}" @class(['active' => $usersActive])>
                <span>{{ __('Manajemen Pengguna') }}</span>
            </a>
        @endif

        @if ($user->isAdmin() && Route::has('admin.criteria.index'))
            <a href="{{ route('admin.criteria.index') }}" @class(['active' => $criteriaActive])>
                <span>{{ __('Manajemen Kriteria') }}</span>
            </a>
        @endif

        @if ($user->isAdmin() && Route::has('admin.influencers.index'))
            <a href="{{ route('admin.influencers.index') }}" @class(['active' => $influencersActive])>
                <span>{{ __('Manajemen Influencer') }}</span>
            </a>
        @endif

        @if (Route::has($sawRoute))
            <a href="{{ route($sawRoute) }}" @class(['active' => $sawActive])>
                <span>{{ __('Perhitungan SAW') }}</span>
            </a>
        @endif
    </nav>

    <div class="user-card">
        <p class="hint">{{ __('Akun') }}</p>
        <strong>{{ $user->username }}</strong>
        <p class="hint">{{ $user->roleLabel() }}</p>

        <form class="mt-4" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-secondary w-full" type="submit">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-inner">
            <div>
                <p class="eyebrow">{{ __('Sistem Pendukung Keputusan') }}</p>
                <strong>{{ __('SPK Influencer SAW') }}</strong>
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-secondary" type="submit">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </header>

    <nav class="mobile-tabs" aria-label="{{ __('Navigasi Seluler') }}">
        <a href="{{ route('dashboard') }}" @class(['pill', 'active' => $dashboardActive])>
            {{ __('Dashboard') }}
        </a>

        @if ($user->isAdmin() && Route::has('admin.users.index'))
            <a href="{{ route('admin.users.index') }}" @class(['pill', 'active' => $usersActive])>
                {{ __('Manajemen Pengguna') }}
            </a>
        @endif

        @if ($user->isAdmin() && Route::has('admin.criteria.index'))
            <a href="{{ route('admin.criteria.index') }}" @class(['pill', 'active' => $criteriaActive])>
                {{ __('Manajemen Kriteria') }}
            </a>
        @endif

        @if ($user->isAdmin() && Route::has('admin.influencers.index'))
            <a href="{{ route('admin.influencers.index') }}" @class(['pill', 'active' => $influencersActive])>
                {{ __('Manajemen Influencer') }}
            </a>
        @endif

        @if (Route::has($sawRoute))
            <a href="{{ route($sawRoute) }}" @class(['pill', 'active' => $sawActive])>
                {{ __('Perhitungan SAW') }}
            </a>
        @endif
    </nav>

    <main class="content">
        @isset($header)
            <section class="section-head" aria-label="{{ __('Judul Halaman') }}">
                <div>
                    {{ $header }}
                </div>
            </section>
        @endisset

        {{ $slot }}
    </main>
</div>
