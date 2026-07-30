<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UniMart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4 font-mono text-sm">
                <a href="{{ route('storefront.home') }}" wire:navigate class="font-semibold text-slate-900">UniMart</a>
                <a href="{{ route('admin.products') }}" wire:navigate class="text-slate-400 hover:text-slate-600">admin</a>
                <a href="{{ route('pos.terminal') }}" wire:navigate class="text-slate-400 hover:text-slate-600">POS</a>
            </div>
            <span class="text-xs text-slate-400">inventory syncs live across storefront &amp; POS</span>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-6 py-10">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
