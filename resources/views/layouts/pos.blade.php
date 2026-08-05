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
        <div class="flex items-center justify-between px-1 pt-3 font-mono text-[11px] uppercase tracking-widest text-ink/40">
            <span>UniMart POS</span>
            @auth
                <div class="flex items-center gap-3">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-amber-dark hover:text-ink">Log out</button>
                    </form>
                </div>
            @endauth
        </div>

        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
