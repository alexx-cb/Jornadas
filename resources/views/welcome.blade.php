<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Conferencias y Talleres - IES Francisco Ayala</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
<div class="min-h-screen bg-gray-50">

    <!-- Header (Menú de Navegación) -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-6 sm:px-8 lg:px-12 flex items-center justify-between">
            <!-- Logo (puedes reemplazar esto con la imagen del logo) -->
            <a href="/" class="text-xl font-semibold text-black hover:text-black/70 transition-colors">
                IES Francisco Ayala
            </a>

            <!-- Menú de Navegación -->
            <nav>
                <ul class="flex items-center space-x-4">
                    @auth
                        <li>
                            <a href="{{ url('/eventos') }}"
                               class="text-gray-700 hover:text-black transition-colors">Ir a Eventos</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}"
                               class="text-gray-700 hover:text-black transition-colors">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li>
                                <a href="{{ route('register') }}"
                                   class="text-gray-700 hover:text-black transition-colors">Register</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section (Sección Principal) -->
    <section class="bg-gradient-to-b from-transparent via-white to-white py-16">
        <div class="max-w-2xl mx-auto text-center">
            <h1 class="text-4xl font-semibold text-black mb-4">
                ¡Descubre tu Futuro en el IES Francisco Ayala!
            </h1>
            <p class="text-xl text-gray-700 mb-8">
                Conferencias y talleres diseñados para inspirarte y prepararte para el éxito.
            </p>
            <a href="{{ route('register') }}"
               class="inline-flex items-center px-6 py-3 bg-[#FF2D20] text-black font-semibold rounded-md shadow-sm hover:bg-[#FF2D20]/90 transition-colors focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:ring-opacity-50">
                Inscríbete Ahora
            </a>
        </div>
    </section>

    <!-- Beneficios Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <h2 class="text-3xl font-semibold text-black text-center mb-8">
                ¿Por qué participar en nuestros eventos?
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Beneficio 1 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-black mb-2">Aprende de Expertos</h3>
                    <p class="text-gray-700">Conéctate con profesionales líderes en sus campos y obtén conocimientos
                        valiosos.</p>
                </div>
                <!-- Beneficio 2 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-black mb-2">Desarrolla Habilidades</h3>
                    <p class="text-gray-700">Participa en talleres prácticos que te permitirán adquirir nuevas
                        habilidades.</p>
                </div>
                <!-- Beneficio 3 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-black mb-2">Amplía tu Red</h3>
                    <p class="text-gray-700">Conoce a otros estudiantes y profesionales, creando conexiones que te
                        ayudarán en el futuro.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-8 border-t border-gray-300">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">
            <p class="text-gray-500">
                &copy; 2025 IES Francisco Ayala. Todos los derechos reservados.
            </p>
            <p class="text-gray-500 mt-2">
                <a href="#" class="hover:underline">Contacto</a> | <a href="#"
                                                                      class="hover:underline">Política de Privacidad</a>
            </p>
        </div>
    </footer>

</div>
</body>

</html>
