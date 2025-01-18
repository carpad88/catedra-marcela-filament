<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cátedra Marcela</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans flex flex-col items-center justify-center min-h-screen">
<img src="{{ asset('img/logo.svg') }}" alt="Logo catedra" class="h-48 mb-20">

<a href="{{ route('login') }}"
   class="border hover:bg-gray-5000 hover:border-black hover:text-red-500 font-light text-gray-600 py-2 px-4 rounded mt-12 flex gap-2 items-center transition-all">
    <x-phosphor-fingerprint-duotone class="size-6"/>
    <span>Iniciar sesión</span>
</a>
</body>
</html>
