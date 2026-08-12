<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024, initial-scale=1">
    <title>UniMart POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@600;700&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        steel: '#EEF0EF',
                        ink: '#14181A',
                        amber: { DEFAULT: '#E8A33D', dark: '#C97F1F' },
                        ledger: '#4C7A5E',
                        stamp: '#C4432B',
                    },
                    fontFamily: {
                        display: ['"Barlow Semi Condensed"', 'sans-serif'],
                        sans: ['"IBM Plex Sans"', 'sans-serif'],
                        mono: ['"IBM Plex Mono"', 'monospace'],
                    },
                },
            },
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .pos-shell { width: 1024px; max-width: 100vw; margin: 0 auto; }
    </style>
</head>
<body class="bg-steel font-sans text-ink antialiased">
    <div class="pos-shell">
        <div class="flex items-center justify-between border-b border-ink/10 bg-white px-4 py-3">
            <span class="font-display text-sm font-bold uppercase tracking-widest text-ink">UniMart <span class="text-amber-dark">POS</span></span>
            @auth
                <div class="flex items-center gap-3">
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.products') }}" wire:navigate class="rounded-sm border border-ink/15 px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide text-ink/50 transition-colors hover:border-ink/30 hover:text-ink">
                            &larr; Admin
                        </a>
                    @endif
                    <span class="font-mono text-[11px] uppercase tracking-wide text-ink/40">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-sm border border-stamp/30 bg-stamp/5 px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide text-stamp transition-colors hover:bg-stamp hover:text-white">Log out</button>
                    </form>
                </div>
            @endauth
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
