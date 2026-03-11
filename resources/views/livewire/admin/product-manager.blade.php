<div>
    <h2 class="intro-y mt-10 text-lg font-medium">Gestión de Productos</h2>
    <div class="mt-5 grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 mt-2 flex flex-wrap items-center sm:flex-nowrap">
            <x-base.button variant="primary" class="mr-2 shadow-md" wire:click="create()">
                Nuevo Producto
            </x-base.button>
            <x-base.button variant="outline-primary" class="mr-2 shadow-md" wire:click="openImportModal">
                <x-base.lucide class="mr-2 h-4 w-4" icon="FilePlus" /> Importar Excel
            </x-base.button>
            <a href="javascript:;" wire:click="downloadTemplate"
                class="text-primary underline flex items-center ml-2 text-xs font-bold">
                <x-base.lucide class="mr-1 h-3 w-3" icon="Download" /> Descargar Plantilla
            </a>
            <div class="mx-auto hidden text-slate-500 md:block">
                Mostrando {{ $products->count() }} productos
            </div>
            <div class="mt-3 w-full sm:ml-auto sm:mt-0 sm:w-auto md:ml-0">
                <div class="relative w-56 text-slate-500">
                    <x-base.form-input class="!box w-56 pr-10" type="text" placeholder="Buscar..." />
                    <x-base.lucide class="absolute inset-y-0 right-0 my-auto mr-3 h-4 w-4" icon="Search" />
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="col-span-12">
                <div class="alert alert-success show mb-2 mt-2" role="alert">{{ session('message') }}</div>
            </div>
        @endif

        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <x-base.table class="-mt-2 border-separate border-spacing-y-[10px]">
                <x-base.table.thead>
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap border-b-0">ID</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0">PRODUCTO</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0">CATEGORÍA</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0">MARCA</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">PRECIO</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">ACCIONES</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach ($products as $product)
                        <x-base.table.tr class="intro-x">
                            <x-base.table.td
                                class="box w-20 rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                {{ $product->id }}
                            </x-base.table.td>
                            <x-base.table.td
                                class="box rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                <a href="javascript:;" class="whitespace-nowrap font-medium">{{ $product->name }}</a>
                            </x-base.table.td>
                            <x-base.table.td
                                class="box rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                {{ $product->category->name ?? 'N/A' }}
                            </x-base.table.td>
                            <x-base.table.td
                                class="box rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                {{ $product->brand->name ?? 'N/A' }}
                            </x-base.table.td>
                            <x-base.table.td
                                class="box rounded-l-none rounded-r-none border-x-0 text-center shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                ${{ number_format($product->price, 2) }}
                            </x-base.table.td>
                            <x-base.table.td
                                class="box w-56 rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                                <div class="flex items-center justify-center">
                                    <a class="mr-3 flex items-center" href="javascript:;"
                                        wire:click="edit({{ $product->id }})">
                                        <x-base.lucide class="mr-1 h-4 w-4" icon="CheckSquare" /> Editar
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;"
                                        wire:click="confirmDelete({{ $product->id }})">
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

    <!-- BEGIN: Modal -->
    @teleport('body')
        <x-base.dialog id="product-modal" wire:ignore.self>
            <x-base.dialog.panel>
                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">
                        {{ $product_id ? 'Editar Producto' : 'Crear Producto' }}
                    </h2>
                </x-base.dialog.title>
                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-12">
                        <x-base.form-label for="name">Nombre</x-base.form-label>
                        <x-base.form-input id="name" type="text" placeholder="Nombre del producto"
                            wire:model="name" />
                        @error('name')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <x-base.form-label for="category">Categoría</x-base.form-label>
                        <x-base.form-select id="category" wire:model="category_id">
                            <option value="">Seleccione...</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </x-base.form-select>
                        @error('category_id')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <x-base.form-label for="brand">Marca</x-base.form-label>
                        <x-base.form-select id="brand" wire:model="brand_id">
                            <option value="">Seleccione...</option>
                            @foreach ($allBrands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </x-base.form-select>
                        @error('brand_id')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <x-base.form-label for="price">Precio</x-base.form-label>
                        <x-base.form-input id="price" type="text" placeholder="0.00" wire:model="price" />
                        @error('price')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                </x-base.dialog.description>
                <x-base.dialog.footer>
                    <x-base.button variant="outline-secondary" class="mr-1 w-20" wire:click="closeModal()">
                        Cancelar
                    </x-base.button>
                    <x-base.button variant="primary" class="w-20" wire:click="store()">
                        Guardar
                    </x-base.button>
                </x-base.dialog.footer>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport
    <!-- END: Modal -->
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
                    <x-base.button class="mr-1 w-24" variant="outline-secondary" data-tw-dismiss="modal" type="button">
                        Cancelar
                    </x-base.button>
                    <x-base.button class="w-24" variant="danger" type="button" wire:click="delete">
                        Eliminar
                    </x-base.button>
                </div>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport

    <!-- BEGIN: Import Modal -->
    @teleport('body')
        <x-base.dialog id="import-modal" wire:ignore.self>
            <x-base.dialog.panel class="p-0">
                <div class="p-5 border-b border-slate-200">
                    <h2 class="font-medium text-base">Importación Masiva de Productos</h2>
                </div>
                <div class="p-5">
                    @if (!$importResults)
                        <div class="p-4 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200 text-center">
                            <x-base.lucide class="mx-auto h-12 w-12 text-slate-400" icon="UploadCloud" />
                            <div class="mt-2 text-slate-500">Seleccione el archivo Excel (.xlsx)</div>
                            <input type="file" wire:model="excelFile" class="mt-4 w-full text-xs"
                                accept=".xlsx, .xls">
                            <div wire:loading wire:target="excelFile" class="mt-2 text-primary font-bold">Cargando
                                archivo...</div>
                        </div>

                        <div class="mt-4 p-3 bg-primary/10 rounded text-xs text-primary">
                            <strong>Instrucciones:</strong>
                            <ul class="list-disc ml-4 mt-1">
                                <li>Use la <a href="javascript:;" wire:click="downloadTemplate"
                                        class="font-bold underline">plantilla oficial</a>.</li>
                                <li>No modifique los encabezados de la Hoja 1.</li>
                                <li>Consulte las Hojas 2 y 3 para ver ID de Categorías y Marcas.</li>
                            </ul>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="text-lg font-bold text-success">Proceso Finalizado</div>
                            <div class="mt-2 flex justify-center gap-4">
                                <div class="p-3 bg-success/10 rounded">
                                    <div class="text-2xl font-black text-success">{{ $importResults['success'] }}</div>
                                    <div class="text-[10px] uppercase font-bold">Exitosos</div>
                                </div>
                                <div class="p-3 bg-danger/10 rounded">
                                    <div class="text-2xl font-black text-danger">{{ count($importResults['failed']) }}
                                    </div>
                                    <div class="text-[10px] uppercase font-bold">Fallidos</div>
                                </div>
                            </div>

                            @if (count($importResults['failed']) > 0)
                                <div class="mt-5 text-left">
                                    <h4 class="font-bold text-danger mb-2">Reporte de Errores:</h4>
                                    <div class="max-h-48 overflow-y-auto border rounded p-2">
                                        @foreach ($importResults['failed'] as $error)
                                            <div class="mb-2 text-xs border-b pb-1 last:border-0">
                                                <span class="font-bold">Fila {{ $error['row'] }}:</span>
                                                {{ $error['name'] }}
                                                <ul class="text-danger mt-1">
                                                    @foreach ($error['errors'] as $msg)
                                                        <li>• {{ $msg }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="p-5 border-t border-slate-200 flex justify-end">
                    <x-base.button variant="outline-secondary" class="mr-1" wire:click="closeImportModal">
                        Cerrar
                    </x-base.button>
                    @if (!$importResults)
                        <x-base.button variant="primary" wire:click="importExcel" wire:loading.attr="disabled"
                            :disabled="!$excelFile">
                            Comenzar Importación
                        </x-base.button>
                    @endif
                </div>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport
    <!-- END: Import Modal -->

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-import-modal', () => {
                const el = document.querySelector("#import-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.show();
                }
            });

            Livewire.on('close-import-modal', () => {
                const el = document.querySelector("#import-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
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

            Livewire.on('open-product-modal', (event) => {
                const el = document.querySelector("#product-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.show();
                }
            });

            Livewire.on('close-product-modal', (event) => {
                const el = document.querySelector("#product-modal");
                if (el) {
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.hide();
                }
            });
        });
    </script>
</div>
