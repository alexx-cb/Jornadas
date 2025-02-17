<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Ponente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="createPonenteForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700">Nombre del Ponente</label>
                        <input type="text" id="nombre" name="nombre" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="fotografia" class="block text-gray-700">Fotografía</label>
                        <input type="file" id="fotografia" name="fotografia" class="mt-1 block w-full" accept="image/*" required>
                    </div>

                    <div class="mb-4">
                        <label for="areas_experiencia" class="block text-gray-700">Áreas de Experiencia</label>
                        <input type="text" id="areas_experiencia" name="areas_experiencia" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="redes_sociales" class="block text-gray-700">Redes Sociales (URL)</label>
                        <input type="text" id="redes_sociales" name="redes_sociales" class="mt-1 block w-full" required>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-black py-2 px-4 rounded-md hover:bg-blue-600">
                            Crear Ponente
                        </button>
                    </div>
                </form>

                <div id="responseMessage" class="mt-4 text-green-500 hidden"></div>
                <div id="errorMessage" class="mt-4 text-red-500 hidden"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('createPonenteForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Previene que el formulario se envíe de forma tradicional


            const nombre = document.getElementById('nombre').value;
            const fotografia = document.getElementById('fotografia').files[0];
            const areas_experiencia = document.getElementById('areas_experiencia').value;
            const redes_sociales = document.getElementById('redes_sociales').value;

            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('fotografia', fotografia);
            formData.append('areas_experiencia', areas_experiencia);
            formData.append('redes_sociales', redes_sociales);


            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('http://localhost:8000/api/ponentes', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        document.getElementById('responseMessage').textContent = 'Ponente creado correctamente.';
                        window.location.href = '/ponentes'
                        document.getElementById('responseMessage').classList.remove('hidden');
                        document.getElementById('errorMessage').classList.add('hidden');
                    } else {
                        document.getElementById('errorMessage').textContent = data.mensaje || 'Hubo un error al crear el ponente.';
                        document.getElementById('errorMessage').classList.remove('hidden');
                        document.getElementById('responseMessage').classList.add('hidden');
                    }
                })
                .catch(error => {
                    document.getElementById('errorMessage').textContent = 'Error de conexión con la API: ' + error.message;
                    document.getElementById('errorMessage').classList.remove('hidden');
                    document.getElementById('responseMessage').classList.add('hidden');
                });
        });
    </script>
</x-app-layout>
