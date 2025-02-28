<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cátedra Marcela</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Literata:ital,wght@1,200..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
<main class="flex justify-center items-center" style="min-height: calc(100vh - 4rem)">
    <div class="px-4 flex justify-center items-center">
        <div class="grid grid-rows-main grid-cols-main gap-1 h-full">

            <a href="/app" class="row-start-1 row-end-2 col-start-2 col-end-4 bg-white">
                <img src="{{ asset('img/logo.svg') }}" alt="Logo catedra" class="h-24 mb-20">
            </a>

            <a href="/app" class="row-start-1 row-end-2 col-start-4 col-end-6 bg-white card">
                <div class="card-img"
                     style="background-image: url('img/catedra.webp'); background-size: cover">
                </div>

                <div
                    class="card-title">
                    <h3 class="leading-none">Cátedra</h3>
                </div>
            </a>

            <a href="/app/projects" class="row-start-2 row-end-2 col-start-2 col-end-4 bg-white card">
                <div
                    class="card-title">
                    <h3 class="leading-none">Proyectos</h3>
                </div>
                <div class="card-img"
                     style="background-image: url('img/project.webp'); background-size: cover">
                </div>
            </a>

            <a href="/app/gallery" class="row-start-2 row-end-2 col-start-4 col-end-6 bg-white card">
                <div
                    class="card-title">
                    <h3 class="leading-none">Galería</h3>
                </div>
                <div class="card-img"
                     style="background-image: url('img/gallery.webp'); background-size: cover">
                </div>
            </a>

            <a href="/app/posts" class="row-start-2 row-end-2 col-start-6 col-end-8 bg-white card">
                <div
                    class="card-title">
                    <h3 class="leading-none">Apuntes</h3>
                </div>

                <div class="card-img"
                     style="background-image: url('img/notes.webp'); background-size: cover; background-position-x: right;">
                </div>
            </a>

            <a href="/app/resources" class="row-start-3 row-end-4 col-start-5 col-end-7 bg-white card">
                <div
                    class="card-title">
                    <h3 class="leading-none">Recursos</h3>
                </div>

                <div class="card-img"
                     style="background-image: url('img/resources.webp'); background-size: cover">
                </div>
            </a>

            <div class="row-start-1 row-end-2 col-start-7 col-end-8 bg-white flex justify-end items-start">
                <a href="/app/login"
                   class="flex items-center font-display font-extralight italic text-xl hover:text-red-500">
                    <x-phosphor-sign-in-duotone class="size-6 mr-1"/>
                    Iniciar sesión
                </a>
            </div>
        </div>
    </div>
</main>

<footer class="h-16 flex justify-center items-center px-4 text-xs font-light text-gray-400">
    2020–{{ now()->year }} | © Marcela Ramírez. Todos los derechos reservados
</footer>

</body>
</html>
