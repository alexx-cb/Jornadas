<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-semibold text-gray-900 mb-8">Historial de Pagos</h1>

        <!-- Tabla de Pagos -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full table-auto w-full">
                <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tipo de Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Fecha de Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Estado</th>
                </tr>
                </thead>
                <tbody id="pagos-table-body" class="bg-white text-gray-700">
                <!-- Los datos se cargarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>

        <!-- Total de Pagos -->
        <div class="mt-8 bg-gray-100 p-6 rounded-lg text-right">
            <p class="text-xl font-semibold">Total de Pagos: <span id="total-pagos">0</span> €</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var totalPagos = 0; // Variable para llevar el total
            function peticion() {
                fetch("http://localhost:8000/api/pagos")
                    .then(data => data.json())
                    .then(respuesta => {
                        if (respuesta.pagos && Array.isArray(respuesta.pagos)) {
                            respuesta.pagos.forEach((datos) => {
                                const correoUsuario = datos.user_id;
                                correoUsuarioPeticion(correoUsuario, datos);
                            });
                        }
                    })
                    .catch(error => console.error('Error al cargar los pagos:', error));
            }

            function correoUsuarioPeticion(correoUsuario, pago) {
                fetch("http://localhost:8000/api/usuarioCaracteristicas/" + correoUsuario)
                    .then(data => data.json())
                    .then(respuesta => {
                        if (respuesta.usuario && respuesta.usuario.email) {
                            // Datos del usuario
                            const email = respuesta.usuario.email;
                            // Insertar fila en la tabla
                            insertarEnTabla(email, pago);
                            // Acumulamos el total
                            totalPagos += parseFloat(pago.cantidad);
                            // Actualizar total de pagos
                            document.getElementById("total-pagos").textContent = totalPagos.toFixed(2);
                        }
                    })
                    .catch(error => console.error('Error al cargar las características del usuario:', error));
            }

            function insertarEnTabla(email, pago) {
                const tbody = document.getElementById("pagos-table-body");

                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="px-6 py-4 text-sm font-medium text-gray-700 border-b border-gray-200">${email}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-700 border-b border-gray-200">${pago.tipo_pago}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-700 border-b border-gray-200">${pago.cantidad} €</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-700 border-b border-gray-200">${new Date(pago.fecha_pago).toLocaleDateString()}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-700 border-b border-gray-200">${pago.estado}</td>
                `;

                tbody.appendChild(row);
            }

            peticion(); // Llamamos la función para cargar los pagos
        });
    </script>
</x-app-layout>
