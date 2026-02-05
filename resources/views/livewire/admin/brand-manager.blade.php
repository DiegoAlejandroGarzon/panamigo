<div>
    <h2 class="intro-y mt-10 text-lg font-medium">Gestión de Marcas</h2>
    <div class="mt-5 grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 mt-2 flex flex-wrap items-center sm:flex-nowrap">
            <x-base.button
                variant="primary"
                class="mr-2 shadow-md"
                wire:click="startCreate()"
                data-tw-toggle="modal"
                data-tw-target="#brand-modal"
            >
                Nueva Marca
            </x-base.button>
            <div class="mx-auto hidden text-slate-500 md:block">
                Mostrando {{ count($brands) }} marcas
            </div>
        </div>

        @if (session()->has('message'))
            <div class="col-span-12">
                <div class="alert alert-success show mb-2 mt-2" role="alert">{{ session('message') }}</div>
            </div>
        @endif

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <x-base.table class="-mt-2 border-separate border-spacing-y-[10px]">
                <x-base.table.thead>
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap border-b-0">ID</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0">NOMBRE</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">ACCIONES</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach ($brands as $brand)
                        <x-base.table.tr class="intro-x">
                            <x-base.table.td class="box w-20 rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                {{ $brand->id }}
                            </x-base.table.td>
                            <x-base.table.td class="box rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                <span class="font-medium">{{ $brand->name }}</span>
                            </x-base.table.td>
                            <x-base.table.td class="box w-56 rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                <div class="flex items-center justify-center">
                                    <a class="mr-3 flex items-center" href="javascript:;" wire:click="edit({{ $brand->id }})" data-tw-toggle="modal" data-tw-target="#brand-modal">
                                        <x-base.lucide class="mr-1 h-4 w-4" icon="CheckSquare" /> Editar
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" wire:click="confirmDelete({{ $brand->id }})">
                                        <x-base.lucide class="mr-1 h-4 w-4" icon="Trash" /> Borrar
                                    </a>
                                </div>
                            </x-base.table.td>
                        </x-base.table.tr>
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    <!-- Modal -->
    @teleport('body')
        <x-base.dialog id="brand-modal" wire:ignore.self>
            <x-base.dialog.panel>
                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">
                        {{ $brand_id ? 'Editar Marca' : 'Crear Marca' }}
                    </h2>
                </x-base.dialog.title>
                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <x-base.form-label for="name">Nombre</x-base.form-label>
                        <x-base.form-input
                            id="name"
                            type="text"
                            placeholder="Nombre de la marca"
                            wire:model="name"
                            wire:keydown.enter="store"
                        />
                        @error('name') <span class="text-danger mt-2">{{ $message }}</span> @enderror
                    </div>
                </x-base.dialog.description>
                <x-base.dialog.footer>
                    <x-base.button
                        type="button"
                        variant="outline-secondary"
                        class="mr-1 w-20"
                        wire:click="closeModal()"
                        data-tw-dismiss="modal"
                    >
                        Cancelar
                    </x-base.button>
                    <x-base.button
                        type="button"
                        variant="primary"
                        class="w-20"
                        wire:click="store"
                    >
                        Guardar
                    </x-base.button>
                </x-base.dialog.footer>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport

    <!-- Delete Confirmation Modal -->
    @teleport('body')
        <x-base.dialog id="delete-confirmation-modal" wire:ignore.self>
            <x-base.dialog.panel>
                <div class="p-5 text-center">
                    <x-base.lucide class="mx-auto mt-3 h-16 w-16 text-danger" icon="XCircle" />
                    <div class="mt-5 text-3xl">¿Estás seguro?</div>
                    <div class="mt-2 text-slate-500">
                        ¿Realmente deseas eliminar este registro? <br />
                        Este proceso no se puede deshacer.
                    </div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <x-base.button
                        class="mr-1 w-24"
                        variant="outline-secondary"
                        data-tw-dismiss="modal"
                        type="button"
                    >
                        Cancelar
                    </x-base.button>
                    <x-base.button
                        class="w-24"
                        variant="danger"
                        type="button"
                        wire:click="delete"
                    >
                        Eliminar
                    </x-base.button>
                </div>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-brand-modal', (event) => {
                const el = document.querySelector("#brand-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.show();
                }
            });
            
            Livewire.on('close-modal', (event) => {
                const brandModalEl = document.querySelector("#brand-modal");
                if (brandModalEl) {
                    const modal = tailwind.Modal.getOrCreateInstance(brandModalEl);
                    modal.hide();
                }
            });

            Livewire.on('open-delete-confirmation', (event) => {
                const el = document.querySelector("#delete-confirmation-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.show();
                }
            });

            Livewire.on('close-delete-confirmation', (event) => {
                const el = document.querySelector("#delete-confirmation-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.hide();
                }
            });
        });
    </script>
</div>
