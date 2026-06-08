<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SPK Influencer SAW') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="min-h-screen bg-[var(--bg)] text-[var(--fg)]">
            <header class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-[var(--gutter)] py-6">
                <a href="/" class="brand" aria-label="Beranda SPK Influencer SAW">
                    <div class="brand-mark">
                        <span style="color: #FFF;">SAW</span>
                    </div>
                    <span>
                        <strong>{{ __('SPK Influencer') }}</strong>
                        <span>{{ __('PT. Behaestex') }}</span>
                    </span>
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
                    @endauth
                @endif
            </header>

            <main class="mx-auto grid w-full max-w-6xl gap-8 px-[var(--gutter)] pb-16 pt-8 lg:grid-cols-[1.1fr_.9fr] lg:items-center lg:pt-16">
                <section>
                    <p class="eyebrow">Sistem Pendukung Keputusan</p>
                    <h1 class="mt-4">Penentuan influencer dengan metode SAW.</h1>
                    <p class="lead mt-6">Aplikasi internal untuk membantu PT. Behaestex mengelola proses penilaian influencer secara terstruktur, transparan, dan siap diaudit.</p>

                    <div class="actions mt-8">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Buka Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">Masuk ke Sistem</a>
                            @endauth
                        @endif
                    </div>
                </section>

                <section class="grid gap-4">
                    <article class="card">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="eyebrow">Ruang Lingkup</p>
                                <h2 class="mt-3 text-2xl">Seleksi berbasis kriteria</h2>
                            </div>
                            <span class="pill accent">SAW</span>
                        </div>
                        <p class="hint mt-4">Sistem memusatkan data influencer, kriteria, bobot, Matriks Keputusan, Matriks Normalisasi, dan Nilai Akhir (Vi).</p>
                    </article>

                    <div class="grid-2">
                        <article class="card">
                            <p class="eyebrow">Admin</p>
                            <p class="mt-3 font-medium">Kelola pengguna dan data proses penilaian.</p>
                        </article>
                        <article class="card">
                            <p class="eyebrow">Manajer</p>
                            <p class="mt-3 font-medium">Tinjau dashboard dan hasil rekomendasi.</p>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
