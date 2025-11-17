@extends('../themes/' . $activeTheme . '/' . $activeLayout)

@section('subhead')
    <title>Perfil - Cambio Foto Perfil</title>
@endsection

@section('subcontent')
    <div class="intro-y mt-8 flex items-center">
        <h2 class="mr-auto text-lg font-medium">Cambiar Foto Perfil</h2>
    </div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-8 2xl:col-span-9">
            <div class="intro-y box lg:mt-5">
                <div class="p-5">
                    <form method="POST" action="{{ route('profile.changeProfilePhoto') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Mostrar la foto de perfil actual -->
                        {{-- <div class="mb-4">
                            <img class="w-32 h-32 rounded-full" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="Foto de Perfil Actual">
                        </div> --}}
                        @if(Auth::user()->profile_photo_path)
                        <div class="mb-4">
                            <img class="w-32 h-32 rounded-full" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="Foto de Perfil Actual">
                        </div>
                        @else
                        <svg class="w-32 h-32 rounded-full" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        @endif

                        <!-- Input para subir una nueva foto de perfil -->
                        <div class="mt-4">
                            <label for="profile_photo" class="form-label">Seleccionar Nueva Foto de Perfil</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control" **accept="image/*"**>
                            @error('profile_photo')
                                <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botón para enviar el formulario -->
                        <x-base.button
                            class="mt-4"
                            type="submit"
                            variant="primary"
                        >
                            Cambiar Foto Perfil
                        </x-base.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
