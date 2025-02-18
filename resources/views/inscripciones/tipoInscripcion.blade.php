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

                    <div id="paypal-button-container">

                    </div>
                    <!-- Botón de envío -->
                    <button type="submit" class="mt-6 bg-blue-500 text-black px-4 py-2 rounded hover:bg-blue-600">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=AZmwcCQe_PVANk6d24YIZnEu84iflMqI_TyCixSqRRxD78KV5iQOtTLT4Iauz2LdHSFwSxRpFZXw3_fY&currency=EUR"></script>
    <script>
        const userId = {{ Auth::user()->id }};
        const userEmail = "{{ Auth::user()->email }}";
        let listaEstudiantes = [];
        let pagoRealizado = false; // Nuevo flag para controlar si el usuario ha pagado

        // Precios de inscripción
        const precios = {
            "1": 15,  // Presencial
            "2": 7,   // Virtual
            "3": 0    // Gratuito
        };

        let selectedTipoInscripcion = "1"; // Presencial por defecto
        let precioActual = precios[selectedTipoInscripcion];

        document.addEventListener("DOMContentLoaded", async function () {
            await obtenerListaEstudiantes();
        });

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

        // Capturar el cambio de tipo de inscripción
        document.getElementById("tipo_inscripcion").addEventListener("change", function () {
            selectedTipoInscripcion = this.value;
            precioActual = precios[selectedTipoInscripcion];
            pagoRealizado = false; // Resetear pago cuando cambie el tipo
            console.log("Nuevo precio:", precioActual);
            renderizarBotonPaypal();
        });

        function renderizarBotonPaypal() {
            document.getElementById("paypal-button-container").innerHTML = "";

            paypal.Buttons({
                style: {
                    'color': 'blue',
                    'shape': 'pill',
                    'label': 'pay'
                },
                createOrder: function (data, actions) {
                    if (precioActual === 0) {
                        alert("No es necesario pagar. La inscripción gratuita se actualizará automáticamente.");
                        pagoRealizado = true;
                        return;
                    }
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: precioActual.toString()
                            }
                        }]
                    });
                },

                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (detalles) {
                        console.log("Pago completado:", detalles);
                        pagoRealizado = true;

                        const pagoData = {
                            user_id: userId,
                            tipo_pago: (selectedTipoInscripcion === "1") ? "Presencial" : (selectedTipoInscripcion === "2") ? "Virtual" : "Gratuito",
                            cantidad: precioActual,
                            fecha_pago: new Date().toISOString().slice(0, 19).replace("T", " "),
                            estado: "Pagado"
                        };

                        enviarPagoAPI(pagoData);
                    });
                },

                onCancel: function (data) {
                    alert("Pago cancelado");
                }
            }).render("#paypal-button-container");
        }

        async function enviarPagoAPI(pagoData) {
            try {
                const response = await fetch("http://localhost:8000/api/pagos", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify(pagoData)
                });

                const data = await response.json();

                if (response.ok) {
                    alert("Pago registrado correctamente en la API");
                } else {
                    alert("Error al registrar el pago en la API");
                }

            } catch (error) {
                console.error("Error al conectar con la API:", error);
                alert("Error de conexión con la API Pagos");
            }
        }

        document.getElementById("updateForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const estudianteSeleccionado = document.querySelector('input[name="estudiante"]:checked').value === "true";
            const esEstudiante = listaEstudiantes.includes(userEmail);
            const errorMensaje = document.getElementById("errorMensaje");

            if (estudianteSeleccionado && !esEstudiante) {
                errorMensaje.classList.remove("hidden");
                alert("No puedes seleccionar 'Estudiante' si tu email no está registrado.");
                return;
            }

            if (!pagoRealizado && precioActual > 0) {
                alert("Debes completar el pago antes de actualizar tu inscripción.");
                return;
            }

            actualizarUsuario(estudianteSeleccionado);
        });

        async function actualizarUsuario(estudianteSeleccionado) {
            const response = await fetch(`http://localhost:8000/api/usuarioCaracteristicas/${userId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ estudiante: estudianteSeleccionado, tipo_inscripcion: selectedTipoInscripcion })
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error("Error al actualizar:", errorData);
                alert("Error de conexión con la API usuario");
                return;
            }

            const data = await response.json();
            alert(data.mensaje);
            window.location.href = "/eventos";
        }

        renderizarBotonPaypal();

    </script>


</x-app-layout>
