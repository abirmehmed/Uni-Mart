<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UniMart Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <span class="font-mono text-sm font-semibold tracking-tight text-slate-900">UniMart <span class="text-indigo-600">/ admin</span></span>
            <span class="text-xs text-slate-400">inventory syncs live across storefront &amp; POS</span>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-6 py-10">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
