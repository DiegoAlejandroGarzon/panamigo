@extends('../themes/' . $activeTheme . '/' . $activeLayout)

@section('subhead')
<title>Usuarios - Actualizar</title>
<link rel="stylesheet" href="{{url('css/blade.css')}}">
@endsection

@section('subcontent')
    <div class="intro-y mt-8 flex items-center">
        <h2 class="mr-auto text-lg font-medium">Actualizar {{isset($profileUpdate) ? ' Perfil' : 'Usuario'}}</h2>
    </div>
    <div class="mt-5 grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 lg:col-span-12">
            <div class="intro-y box p-5">
            @if(isset($profileUpdate))
            <form method="POST" action="{{ route('profile.update', ['id' => $user->id]) }}">
            @else
            <form method="POST" action="{{ route('users.update', ['id' => $user->id]) }}">
            @endif
                @csrf
                <div class="intro-y col-span-12 lg:col-span-6">
                    <x-base.form-label for="name">Nombre Completo</x-base.form-label>

                    <div class="grid-cols-2 gap-2 sm:grid">
                        <x-base.form-input
                            class="w-full {{ $errors->has('name') ? 'border-red-500' : '' }}"
                            id="name"
                            name="name"
                            type="text"
                            placeholder="Nombres"
                            value="{{ old('name', $user->name) }}"
                        />
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror

                        <x-base.form-input
                            class="w-full {{ $errors->has('lastname') ? 'border-red-500' : '' }}"
                            id="lastname"
                            name="lastname"
                            type="text"
                            placeholder="Apellidos"
                            value="{{ old('lastname', $user->lastname) }}"
                        />
                        @error('lastname')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row row_documento_id">
                    <div class="col mt-3 col_document_type">
                        <x-base.form-label for="type_document">Tipo de Documento</x-base.form-label>
                        <x-base.tom-select
                            class="w-full {{ $errors->has('type_document') ? 'border-red-500' : '' }}"
                            id="type_document"
                            name="type_document"
                        >
                            <option value="CC" {{ old('type_document', $user->type_document) == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="TI" {{ old('type_document', $user->type_document) == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="CE" {{ old('type_document', $user->type_document) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <!-- Agrega más opciones según sea necesario -->
                        </x-base.tom-select>
                        @error('type_document')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col mt-3 col_document_id">
                        <x-base.form-label for="document_number">Número de Documento</x-base.form-label>
                        <x-base.form-input
                            class="w-full {{ $errors->has('document_number') ? 'border-red-500' : '' }}"
                            id="document_number"
                            name="document_number"
                            type="text"
                            placeholder="Número de Documento"
                            value="{{ old('document_number', $user->document_number) }}"
                        />
                        @error('document_number')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row row_phone_cumpleaños">

                    <div class="col mt-3 col_cumpleaños">
                        <x-base.form-label for="birth_date">Fecha Nacimiento</x-base.form-label>
                        <x-base.form-input
                            class="w-full {{ $errors->has('birth_date') ? 'border-red-500' : '' }}"
                            id="birth_date"
                            name="birth_date"
                            type="date"
                            value="{{ old('birth_date', $user->birth_date) }}"
                        />
                        @error('birth_date')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col mt-3 col_phone">
                        <x-base.form-label for="phone">Número de Teléfono</x-base.form-label>
                        <x-base.form-input
                            class="w-full {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                            id="phone"
                            name="phone"
                            type="text"
                            placeholder="Número de Teléfono"
                            value="{{ old('phone', $user->phone) }}"
                        />
                        @error('phone')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>


                </div>

                <div class="mt-3">
                    <x-base.form-label for="email">Correo Electrónico</x-base.form-label>

                    <x-base.form-input
                        class="w-full {{ $errors->has('email') ? 'border-red-500' : '' }}"
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Email"
                        value="{{ old('email', $user->email) }}"
                    />
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                @if(!isset($profileUpdate))
                <div class="mt-3">
                    <x-base.form-label for="role_id">Role</x-base.form-label>
                    <x-base.tom-select
                        class="w-full {{ $errors->has('role_id') ? 'border-red-500' : '' }}"
                        id="role_id"
                        name="role_id"
                    >
                    <option></option>
                    @foreach ($roles as $rol)
                        <option value="{{$rol->id}}" {{ old('role_id', $user->roles[0]->id) == $rol->id ? 'selected' : '' }}>{{ $rol->name }}</option>
                    @endforeach
                    </x-base.tom-select>
                    @error('role_id')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="row row_dptocity">

                @if(!isset($profileUpdate))
                <div class="mt-3">
                    <label>Status</label>
                    <x-base.form-switch class="mt-2">
                        <x-base.form-switch.input type="checkbox" name="status_toggle" id="status-toggle" value="1" />
                        <input type="hidden" name="status" id="status-hidden" value="0">
                    </x-base.form-switch>
                </div>
                @endif

                <div class="mt-5 text-right">
                    <x-base.button
                        class="mr-1 w-24"
                        type="button"
                        variant="outline-secondary"
                        onclick="window.location='{{ url()->previous() }}'"
                    >
                        Cancelar
                    </x-base.button>
                    <x-base.button
                        class="w-24"
                        type="submit"
                        variant="primary"
                    >
                        Actualizar
                    </x-base.button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <script>
        @if (!isset($profileUpdate))
        document.getElementById('status-toggle').addEventListener('change', function() {
            document.getElementById('status-hidden').value = this.checked ? '1' : '0';
        });
        @endif

        document.addEventListener('DOMContentLoaded', function() {

            @if (!isset($profileUpdate))
                // Obtén el valor del estado desde un atributo de datos o directamente desde una variable de JavaScript
                var statusValue = @json($user->status); // Se convierte el valor a una cadena de JavaScript

                var checkbox = document.getElementById('status-toggle');
                var hiddenInput = document.getElementById('status-hidden');

                // Establece el estado del checkbox
                checkbox.checked = statusValue == '1';

                // Actualiza el valor del input oculto
                checkbox.addEventListener('change', function() {
                    hiddenInput.value = this.checked ? '1' : '0';
                });
            @endif

            // Establece el valor del departamento y filtra ciudades
            var departmentId = @json($user->department_id);
            var cityId = @json($user->city_id);

            if (departmentId) {
                // Filtra las ciudades basadas en el departamento del usuario
                filterCities(departmentId, cityId);
            }
        });

        function updateCityOptions(cities, selectedCityId) {
            var citySelect = document.querySelector('#city_id').tomselect;

            // Verifica si 'cities' es un array
            if (!Array.isArray(cities)) {
                console.error('Expected an array of cities but got:', cities);
                return;
            }

            // Limpia todas las opciones actuales
            citySelect.clearOptions();

            // Agrega nuevas opciones dinámicamente
            cities.forEach(city => {
                citySelect.addOption({value: city.id, text: city.name});
            });

            // Establece la ciudad seleccionada
            if (selectedCityId) {
                citySelect.setValue(selectedCityId);
            }

            // Refresca la lista de opciones para que se muestren correctamente en la interfaz
            citySelect.refreshOptions(false);
        }

        function filterCities(departmentId, selectedCityId = null) {
            var citySelect = document.getElementById('city_id');

            // Limpia el select de ciudades
            citySelect.innerHTML = '<option></option>';

            // Verifica si departmentId está definido
            if (departmentId) {
                fetch(`/cities/${departmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        updateCityOptions(data.cities, selectedCityId);
                    });
            }
        }

        // Establece el valor del campo 'city_id' en el formulario
        document.addEventListener('DOMContentLoaded', function() {
            var selectedCityId = @json($user->city_id);
            var departmentId = document.getElementById('department_id').value;

            // Filtra ciudades si ya hay un departamento seleccionado
            if (departmentId) {
                filterCities(departmentId, selectedCityId);
            }

            // Actualiza el select de ciudades si se cambia el departamento
            document.getElementById('department_id').addEventListener('change', function() {
                var selectedDepartmentId = this.value;
                filterCities(selectedDepartmentId);
            });
        });
    </script>
@endsection
