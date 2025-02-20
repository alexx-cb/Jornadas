<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Evento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="eventoForm" onsubmit="crearEvento(event)">
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700">Nombre del Evento</label>
                        <input type="text" id="nombre" name="nombre" class="w-full p-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="block text-gray-700">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="w-full p-2 border border-gray-300 rounded-md" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="ponente_id" class="block text-gray-700">Ponente</label>
                        <select id="ponente_id" name="ponente_id" class="w-full p-2 border border-gray-300 rounded-md" required>
                            <option value="">Seleccionar Ponente</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="tipo_evento" class="block text-gray-700">Tipo de Evento</label>
                        <select id="tipo_evento" name="tipo_evento" class="w-full p-2 border border-gray-300 rounded-md" required>
                            <option value="">Seleccionar Tipo</option>
                            <option value="Taller">Taller</option>
                            <option value="Conferencia">Conferencia</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="dia" class="block text-gray-700">Día</label>
                        <select id="dia" name="dia" class="w-full p-2 border border-gray-300 rounded-md" required>
                            <option value="">Seleccionar Día</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="hora_inicio" class="block text-gray-700">Hora de Inicio</label>
                        <select id="hora_inicio" name="hora_inicio" class="w-full p-2 border border-gray-300 rounded-md" required>
                            <option value="">Seleccionar Hora</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="cupo_maximo" class="block text-gray-700">Cupo Máximo</label>
                        <input type="number" id="cupo_maximo" name="cupo_maximo" class="w-full p-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 text-black py-2 px-4 rounded-md hover:bg-blue-600">
                            Crear Evento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            try {
                const ponentes = await obtenerPonentes();
                const horasDisponibles = await obtenerHorasDisponibles();
                llenarSelectPonentes(ponentes);
                llenarSelectHoras(horasDisponibles);
            } catch (error) {
                console.error("Error general:", error);
            }
        });

        async function obtenerPonentes() {
            try {
                let response = await fetch("http://localhost:8000/api/ponentes");
                let data = await response.json();
                return data.ponentes || [];
            } catch (error) {
                console.error("Error al obtener ponentes:", error);
                return [];
            }
        }

        async function obtenerHorasDisponibles() {
            const todasLasHoras = ["10:00", "11:00", "12:30", "13:30", "17:00", "18:00", "19:30"];
            try {
                let response = await fetch("http://localhost:8000/api/eventos");
                let data = await response.json();
                if (!data.eventos) return todasLasHoras;

                let horasOcupadas = data.eventos.map(evento => evento.hora_inicio);
                return todasLasHoras.filter(hora => !horasOcupadas.includes(hora));
            } catch (error) {
                console.error("Error al obtener eventos:", error);
                return todasLasHoras;
            }
        }

        function llenarSelectPonentes(ponentes) {
            const selectPonente = document.getElementById("ponente_id");
            ponentes.forEach(ponente => {
                let option = document.createElement("option");
                option.value = ponente.id;
                option.textContent = ponente.nombre;
                selectPonente.appendChild(option);
            });
        }

        function llenarSelectHoras(horas) {
            const selectHora = document.getElementById("hora_inicio");
            horas.forEach(hora => {
                let option = document.createElement("option");
                option.value = hora;
                option.textContent = hora;
                selectHora.appendChild(option);
            });
        }

        function crearEvento(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById("eventoForm"));

            let datos = {
                nombre: formData.get("nombre"),
                descripcion: formData.get("descripcion"),
                ponente_id: formData.get("ponente_id"),
                tipo_evento: formData.get("tipo_evento"),
                dia: formData.get("dia"),
                hora_inicio: formData.get("hora_inicio"),
                cupo_maximo: parseInt(formData.get("cupo_maximo")),
                cupo_actual: []
            };

            fetch("http://localhost:8000/api/eventos", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.mensaje);
                    window.location.href = "/eventos";
                })
                .catch(error => {
                    if (error.errors) {
                        let errorMessage = "Errores de validación:\n";
                        for (let field in error.errors) {
                            errorMessage += `${field}: ${error.errors[field].join(', ')}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        console.error("Error en la solicitud:", error);
                        alert("Error de conexión con la API");
                    }
                });
        }

    </script>
</x-app-layout>
