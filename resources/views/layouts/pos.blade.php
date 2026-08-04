<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024, initial-scale=1">
    <title>UniMart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .pos-shell { width: 1024px; max-width: 100vw; margin: 0 auto; }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <div class="pos-shell">
        <div class="flex items-center justify-between px-1 pt-3 text-xs text-slate-400">
            <span class="font-mono">UniMart POS</span>
            @auth
                <div class="flex items-center gap-3">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-indigo-600 hover:text-indigo-500">Log out</button>
                    </form>
                </div>
            @endauth
        </div>

        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
