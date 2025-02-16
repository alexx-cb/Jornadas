<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ponentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(auth()->check() && auth()->user()->hasRole('admin')) <!-- Verifica si el usuario tiene el rol admin -->
                <a href="{{ route('ponentes.crear') }}" class="bg-blue-500 text-black py-2 px-4 rounded-md hover:bg-blue-600">
                    Crear Ponente
                </a>
                @endif
                <div id="ponentesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Aquí se cargarán los ponentes dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("http://localhost:8000/api/ponentes")
                .then(response => response.json())
                .then(data => {
                    if (data.ponentes && Array.isArray(data.ponentes)) {
                        const container = document.getElementById("ponentesContainer");
                        data.ponentes.forEach(ponente => {
                            // Reemplazamos las barras invertidas con barras normales
                            const imageUrl = `/${ponente.fotografia.replace(/\\/g, '/')}`;
                            const card = `
                            <div class="bg-white rounded-xl shadow-lg p-15 w-full max-w-md mx-auto transform transition duration-500 hover:scale-105">
                                <div class="flex flex-col items-center">
                                    <img src="${imageUrl}" alt="${ponente.nombre}" class="w-48 h-48 object-cover rounded-full border-4 border-gray-200 shadow-md">
                                    <h3 class="text-2xl font-semibold mt-6 text-gray-800">${ponente.nombre}</h3>
                                    <p class="text-gray-600 mt-3 text-center px-6">Áreas de experiencia: <span class="font-medium">${ponente.areas_experiencia}</span></p>
                                    <a href="${ponente.redes_sociales}" target="_blank" class="text-blue-500 hover:underline mt-4 text-lg">🔗 Redes Sociales</a>
                                    <p class="text-gray-500 text-sm mt-3">📅 Registrado: ${new Date(ponente.created_at).toLocaleDateString()}</p>
                                </div>
                            </div>
                        `;
                            container.innerHTML += card;
                        });
                    }
                })
                .catch(error => console.error("Error al cargar los ponentes:", error));
        });
    </script>
</x-app-layout>
