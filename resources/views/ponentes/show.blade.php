<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ponentes') }}
        </h2>
    </x-slot>
    <script>
        window.isAdmin = {{ auth()->check() && auth()->user()->hasRole('admin') ? 'true' : 'false' }};
    </script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(auth()->check() && auth()->user()->hasRole('admin'))
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
                        container.innerHTML = '';

                        data.ponentes.forEach(ponente => {
                            const imageUrl = `/${ponente.fotografia.replace(/\\/g, '/')}`;
                            let card = `
                                <div class="bg-white rounded-xl shadow-lg p-15 w-full max-w-md mx-auto transform transition duration-500 hover:scale-105">
                                    <div class="flex flex-col items-center">
                                        <img src="${imageUrl}" alt="${ponente.nombre}" class="w-48 h-48 object-cover rounded-full border-4 border-gray-200 shadow-md">
                                        <h3 class="text-2xl font-semibold mt-6 text-gray-800">${ponente.nombre}</h3>
                                        <p class="text-gray-600 mt-3 text-center px-6">Áreas de experiencia: <span class="font-medium">${ponente.areas_experiencia}</span></p>
                                        <a href="${ponente.redes_sociales}" target="_blank" class="text-blue-500 hover:underline mt-4 text-lg">🔗 Redes Sociales</a>
                                        <p class="text-gray-500 text-sm mt-3">📅 Registrado: ${new Date(ponente.created_at).toLocaleDateString()}</p>
                                        <div class="flex gap-2 mt-4">
                                            ${window.isAdmin ? `
                                                <a href="/ponentes/${ponente.id}/editar" class="bg-green-500 text-black py-2 px-4 rounded-md hover:bg-green-600">Editar</a>
                                                <button onclick="eliminarPonente(${ponente.id})" class="bg-red-500 text-black py-2 px-4 rounded-md hover:bg-red-600">Eliminar</button>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.innerHTML += card;
                        });
                    }
                })
                .catch(error => console.error("Error al cargar los ponentes:", error));
        });

        // Función para eliminar ponente
        function eliminarPonente(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este ponente?')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`http://localhost:8000/api/ponentes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 200) {
                            alert('Ponente eliminado correctamente');
                            window.location.reload();
                        } else {
                            alert('Error al eliminar el ponente: ' + data.mensaje);
                        }
                    })
                    .catch(error => {
                        console.error('Error al eliminar el ponente:', error);
                        alert('No se puede eliminar el ponente');
                    });
            }
        }
    </script>
</x-app-layout>
