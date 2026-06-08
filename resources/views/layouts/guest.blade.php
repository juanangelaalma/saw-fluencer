<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SPK Influencer SAW') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="min-h-screen bg-[var(--bg)] text-[var(--fg)]">
            <main class="mx-auto grid min-h-screen w-full max-w-6xl items-center gap-8 px-[var(--gutter)] py-10 lg:grid-cols-[1fr_440px]">
                <section class="hidden lg:block">
                    <a href="/" class="brand w-max" aria-label="Beranda SPK Influencer SAW">
                        <span class="brand-mark">SAW</span>
                        <span>
                            <strong>SPK Influencer SAW</strong>
                            <span>PT. Behaestex</span>
                        </span>
                    </a>

                    <div class="mt-12 max-w-2xl">
                        <p class="eyebrow">Akses Sistem</p>
                        <h1 class="mt-4">Masuk ke sistem pendukung keputusan influencer.</h1>
                        <p class="lead mt-5">Gunakan akun internal yang telah diberikan admin. Peran pengguna ditentukan oleh data akun, bukan pilihan di halaman masuk.</p>
                    </div>
                </section>

                <section class="card w-full">
                    <div class="mb-8 lg:hidden">
                        <a href="/" class="brand w-max" aria-label="Beranda SPK Influencer SAW">
                            <span class="brand-mark">SAW</span>
                            <span>
                                <strong>SPK Influencer SAW</strong>
                                <span>PT. Behaestex</span>
                            </span>
                        </a>
                    </div>

                    {{ $slot }}
                </section>
            </main>
        </div>
    </body>
</html>
