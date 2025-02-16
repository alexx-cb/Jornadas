<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Ponente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div id="editForm">
                    <form id="editPonenteForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                        </div>

                        <div class="mb-4">
                            <label for="areas_experiencia" class="block text-sm font-medium text-gray-700">Áreas de Experiencia</label>
                            <input type="text" name="areas_experiencia" id="areas_experiencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                        </div>

                        <div class="mb-4">
                            <label for="redes_sociales" class="block text-sm font-medium text-gray-700">Redes Sociales</label>
                            <input type="url" name="redes_sociales" id="redes_sociales" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                        </div>

                        <div class="mb-4">
                            <label for="fotografia" class="block text-sm font-medium text-gray-700">Fotografía</label>
                            <input type="file" name="fotografia" id="fotografia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required/>
                        </div>

                        <button type="submit" class="bg-blue-500 text-black px-4 py-2 rounded-md">Actualizar</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ponenteId = "{{ $id }}"; // Obtén el ID desde la URL

            // Realizamos el fetch para obtener los datos del ponente
            fetch(`http://localhost:8000/api/ponentes/${ponenteId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.ponente) {
                        document.getElementById('nombre').value = data.ponente.nombre;
                        document.getElementById('areas_experiencia').value = data.ponente.areas_experiencia;
                        document.getElementById('redes_sociales').value = data.ponente.redes_sociales;
                    } else {
                        alert("No se pudo cargar los datos del ponente.");
                    }
                })
                .catch(error => console.error("Error al obtener los datos del ponente:", error));

            // Cuando se envíe el formulario
            document.getElementById('editPonenteForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData();
                formData.append('_method', 'PUT'); // Emular PUT
                formData.append('nombre', document.getElementById('nombre').value);
                formData.append('areas_experiencia', document.getElementById('areas_experiencia').value);
                formData.append('redes_sociales', document.getElementById('redes_sociales').value);

                const fotografiaInput = document.getElementById('fotografia');
                if (fotografiaInput.files.length > 0) {
                    formData.append('fotografia', fotografiaInput.files[0]);
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`http://localhost:8000/api/ponentes/${ponenteId}`, {
                    method: 'POST', // Usamos POST para el FormData
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 200) {
                            alert('Ponente actualizado correctamente');
                            window.location.href = '/ponentes'; // Opcional: redirigir después de actualizar
                        } else {
                            let errores = '';
                            if (data.errores) {
                                for (let key in data.errores) {
                                    errores += `${key}: ${data.errores[key].join(', ')}\n`;
                                }
                            }
                            alert('Error al actualizar el ponente: ' + (data.mensaje || 'Desconocido') + '\n' + errores);
                        }
                    })
                    .catch(error => {
                        console.error('Error al actualizar el ponente:', error);
                        alert('Error de conexión con la API');
                    });
            });
        });

    </script>
</x-app-layout>
