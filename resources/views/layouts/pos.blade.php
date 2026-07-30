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
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
