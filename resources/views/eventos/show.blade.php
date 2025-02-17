<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Eventos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <a href="{{ route('eventos.crear') }}" class="bg-blue-500 text-black py-2 px-4 rounded-md hover:bg-blue-600">
                        Crear Evento
                    </a>
                @endif
                <div id="eventosContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-6">
                    <!-- Aquí se cargarán los eventos dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <script>
        window.isAdmin = {{ auth()->check() && auth()->user()->hasRole('admin') ? 'true' : 'false' }};

        document.addEventListener("DOMContentLoaded", async function() {
            try {
                const eventos = await obtenerEventos(); // 1️⃣ Obtener eventos
                const ponentesInfo = await obtenerPonentes(eventos); // 2️⃣ Obtener ponentes
                maquetarVista(eventos, ponentesInfo); // 3️⃣ Renderizar vista
            } catch (error) {
                console.error("Error general:", error);
            }
        });

        async function obtenerEventos() {
            try {
                let response = await fetch("http://localhost:8000/api/eventos");
                let data = await response.json();

                if (!data.eventos || !Array.isArray(data.eventos)) {
                    console.error("No hay eventos disponibles.");
                    return [];
                }

                return data.eventos;
            } catch (error) {
                console.error("Error al obtener eventos:", error);
                return [];
            }
        }

        async function obtenerPonentes(eventos) {
            let ponentesInfo = {};
            let uniquePonenteIds = [...new Set(eventos.map(e => e.ponente_id))];

            let ponentePromises = uniquePonenteIds.map(id =>
                fetch(`http://localhost:8000/api/ponentes/${id}`)
                    .then(res => res.json())
                    .then(ponenteData => {
                        ponentesInfo[id] = ponenteData.ponente.nombre || "No disponible";
                    })
                    .catch(() => {
                        ponentesInfo[id] = "No disponible";
                    })
            );

            await Promise.all(ponentePromises);
            return ponentesInfo;
        }

        function maquetarVista(eventos, ponentesInfo) {
            const container = document.getElementById("eventosContainer");
            container.innerHTML = ''; // Limpiar antes de cargar los datos

            eventos.forEach(evento => {
                let card = `
                    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-auto transform transition duration-500 hover:scale-105">
                        <h3 class="text-2xl font-semibold text-gray-800">${evento.nombre}</h3>
                        <p class="text-gray-600 mt-2">📅 Día: <span class="font-medium">${evento.dia}</span></p>
                        <p class="text-gray-600">🕒 Hora: ${evento.hora_inicio} - ${evento.hora_fin}</p>
                        <p class="text-gray-600">🎤 Ponente: ${ponentesInfo[evento.ponente_id]}</p>
                        <p class="text-gray-600">📝 Tipo: <span class="font-medium">${evento.tipo_evento}</span></p>
                        <p class="text-gray-600 mt-2">📖 Descripción: ${evento.descripcion}</p>
                        <p class="text-gray-600">👥 Cupo: ${evento.cupo_actual} / ${evento.cupo_maximo}</p>
                        <div class="flex gap-2 mt-4">
                            ${window.isAdmin ? `
                                <button onclick="eliminarEvento(${evento.id})" class="bg-red-500 text-black py-2 px-4 rounded-md hover:bg-red-600">Eliminar</button>
                            ` : ''}
                        </div>
                    </div>
                `;
                container.innerHTML += card;
            });
        }

        function eliminarEvento(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este evento?')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`http://localhost:8000/api/eventos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 200) {
                            alert('Evento eliminado correctamente');
                            window.location.reload();
                        } else {
                            alert('Error al eliminar el evento: ' + (data.mensaje || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error al eliminar el evento:', error);
                        alert('Error de conexión con la API');
                    });
            }
        }
    </script>


</x-app-layout>
