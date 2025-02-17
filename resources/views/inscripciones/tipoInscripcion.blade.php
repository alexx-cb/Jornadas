<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Actualizar Características de Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Actualizar Inscripción</h3>

                <form id="updateForm">
                    @csrf

                    <!-- Select de Tipo de Inscripción -->
                    <label for="tipo_inscripcion" class="block text-sm font-medium text-gray-700">Tipo de Inscripción:</label>
                    <select id="tipo_inscripcion" name="tipo_inscripcion" class="mt-1 p-2 border border-gray-300 rounded w-full">
                        <option value="1">Presencial</option>
                        <option value="2">Virtual</option>
                        <option value="3">Gratuito</option>
                    </select>

                    <!-- Radio para Estudiante -->
                    <div class="mt-4">
                        <span class="text-sm font-medium text-gray-700">¿Eres estudiante?</span>
                        <label class="ml-4">
                            <input type="radio" name="estudiante" value="true" id="estudianteSi"> Sí
                        </label>
                        <label class="ml-4">
                            <input type="radio" name="estudiante" value="false" id="estudianteNo" checked> No
                        </label>
                    </div>

                    <!-- Mensaje de error -->
                    <p id="errorMensaje" class="text-red-500 text-sm mt-2 hidden">No puedes seleccionar "Gratuito" o "Estudiante" si tu email no está registrado.</p>

                    <!-- Botón de envío -->
                    <button type="submit" class="mt-6 bg-blue-500 text-black px-4 py-2 rounded hover:bg-blue-600">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 1️⃣ Obtener datos del usuario autenticado desde Blade
        const userId = {{ Auth::user()->id }};
        const userEmail = "{{ Auth::user()->email }}";
        let listaEstudiantes = [];

        document.addEventListener("DOMContentLoaded", async function () {
            await obtenerListaEstudiantes();
        });

        // 2️⃣ Obtener la lista de estudiantes desde la API
        async function obtenerListaEstudiantes() {
            try {
                const response = await fetch("http://localhost:8000/api/estudiantes");
                const data = await response.json();

                if (data.estudiantes && Array.isArray(data.estudiantes)) {
                    listaEstudiantes = data.estudiantes.map(est => est.email);
                }
            } catch (error) {
                console.error("Error al obtener la lista de estudiantes:", error);
            }
        }

        // 3️⃣ Manejar el envío del formulario
        document.getElementById("updateForm").addEventListener("submit", async function (event) {
            event.preventDefault();

            const estudianteSeleccionado = document.querySelector('input[name="estudiante"]:checked').value === "true";
            const tipoInscripcion = document.getElementById("tipo_inscripcion").value;
            const esEstudiante = listaEstudiantes.includes(userEmail);
            const errorMensaje = document.getElementById("errorMensaje");

            // Validar si el usuario puede seleccionar "Estudiante" o "Gratuito"
            if ((estudianteSeleccionado || tipoInscripcion === "3") && !esEstudiante) {
                errorMensaje.classList.remove("hidden");
                return;
            } else {
                errorMensaje.classList.add("hidden");
            }

            // Enviar la actualización
            try {
                const response = await fetch(`http://localhost:8000/api/usuarioCaracteristicas/${userId}`, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ estudiante: estudianteSeleccionado, tipo_inscripcion: tipoInscripcion })
                });

                const data = await response.json();

                alert(data.mensaje);
                window.location.href = "/eventos"
            } catch (error) {
                console.error("Error al actualizar:", error);
                alert("Error de conexión con la API");
            }
        });
    </script>

</x-app-layout>
