<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Usuarios</h3>

                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Email</th>
                            <th class="border px-4 py-2">Tipo Inscripción</th>
                            <th class="border px-4 py-2">Estudiante</th>
                            <th class="border px-4 py-2">Rol</th>
                            <th class="border px-4 py-2">Creado</th>
                        </tr>
                        </thead>
                        <tbody id="usuariosTable">
                        <!-- Los datos se llenarán aquí con JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("http://localhost:8000/api/usuarioCaracteristicas")
                .then(response => response.json())
                .then(data => {
                    if (data.usuarios && Array.isArray(data.usuarios)) {
                        const tableBody = document.getElementById("usuariosTable");
                        data.usuarios.forEach(usuario => {
                            const row = `
                                <tr>
                                    <td class="border px-4 py-2">${usuario.id}</td>
                                    <td class="border px-4 py-2">${usuario.email}</td>
                                    <td class="border px-4 py-2">${usuario.tipo_inscripcion}</td>
                                    <td class="border px-4 py-2">${usuario.estudiante ? "Sí" : "No"}</td>
                                    <td class="border px-4 py-2">${usuario.rol}</td>
                                    <td class="border px-4 py-2">${new Date(usuario.created_at).toLocaleString()}</td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    }
                })
                .catch(error => console.error("Error al cargar los usuarios:", error));
        });
    </script>
</x-app-layout>
