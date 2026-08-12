<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UniMart</title>
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
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-steel font-sans text-ink antialiased">
    <nav class="border-b border-ink/10 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('storefront.home') }}" wire:navigate class="font-display text-xl font-bold uppercase tracking-wide text-ink">
                    UniMart
                </a>
                @auth
                    <div class="flex items-center gap-1 rounded-sm border border-ink/10 bg-steel/60 p-1">
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('admin.products') }}" wire:navigate
                                class="rounded-sm px-3 py-1 font-mono text-[11px] uppercase tracking-wide transition-colors {{ request()->routeIs('admin.*') ? 'bg-ink text-white shadow-sm' : 'text-ink/50 hover:text-ink' }}">
                                Admin
                            </a>
                        @endif
                        <a href="{{ route('pos.terminal') }}" wire:navigate
                            class="rounded-sm px-3 py-1 font-mono text-[11px] uppercase tracking-wide transition-colors {{ request()->routeIs('pos.*') ? 'bg-ink text-white shadow-sm' : 'text-ink/50 hover:text-ink' }}">
                            POS
                        </a>
                    </div>
                @endauth
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-sm border border-ledger/30 bg-ledger/5 px-2.5 py-1 font-mono text-[11px] uppercase tracking-wide text-ledger">
                    <span class="relative flex h-1.5 w-1.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-ledger opacity-75"></span><span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-ledger"></span></span>
                    Inventory synced live
                </span>
                @auth
                    <span class="font-mono text-[11px] uppercase tracking-wide text-ink/40">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-sm border border-stamp/30 bg-stamp/5 px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide text-stamp transition-colors hover:bg-stamp hover:text-white">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="rounded-sm border border-amber-dark/40 bg-white px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide text-amber-dark transition-colors hover:bg-amber-dark hover:text-white">Staff login</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-6xl px-6 py-10">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
