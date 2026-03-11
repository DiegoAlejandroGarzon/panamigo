<div>
    <h2 class="intro-y mt-10 text-lg font-medium">Gestión de Productos</h2>
    <div class="mt-5 grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 mt-2 flex flex-wrap items-center sm:flex-nowrap">
            <!-- Nuevo Producto: Alpine para resetear sin petición al servidor -->
            <x-base.button variant="primary" class="mr-2 shadow-md" data-tw-toggle="modal" data-tw-target="#product-modal"
                @click="$wire.set('product_id', null, false); $wire.set('name', '', false); $wire.set('price', '', false); $wire.set('stock', '', false); $wire.set('category_id', null, false); $wire.set('brand_id', null, false);">
                Nuevo Producto
            </x-base.button>

            <!-- Importar Excel: Sin wire:click para abrir el modal -->
            <x-base.button variant="outline-primary" class="mr-2 shadow-md" data-tw-toggle="modal"
                data-tw-target="#import-modal">
                <x-base.lucide class="mr-2 h-4 w-4" icon="FilePlus" /> Importar Excel
            </x-base.button>

            <a href="javascript:;" wire:click="downloadTemplate"
                class="text-primary underline flex items-center ml-2 text-xs font-bold">
                <x-base.lucide class="mr-1 h-3 w-3" icon="Download" /> Descargar Plantilla
            </a>
            <div class="mx-auto hidden text-slate-500 md:block">
                Mostrando {{ $products->count() }} productos
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
                                <span class="whitespace-nowrap font-medium">{{ $product->name }}</span>
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

    <!-- BEGIN: Product Modal -->
    @teleport('body')
        <x-base.dialog id="product-modal" wire:ignore.self>
            <x-base.dialog.panel>
                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">
                        {{ $product_id ? 'Editar Producto' : 'Crear Producto' }}
                    </h2>
                </x-base.dialog.title>
                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3 text-slate-800">
                    <div class="col-span-12 sm:col-span-12">
                        <x-base.form-label for="name">Nombre</x-base.form-label>
                        <x-base.form-input id="name" type="text" placeholder="Nombre del producto"
                            wire:model="name" />
                        @error('name')
                            <span class="text-danger mt-2 text-xs font-bold">{{ $message }}</span>
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
                            <span class="text-danger mt-2 text-xs font-bold">{{ $message }}</span>
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
                            <span class="text-danger mt-2 text-xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <x-base.form-label for="price">Precio</x-base.form-label>
                        <x-base.form-input id="price" type="text" placeholder="0.00" wire:model="price" />
                        @error('price')
                            <span class="text-danger mt-2 text-xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </x-base.dialog.description>
                <x-base.dialog.footer>
                    <x-base.button variant="outline-secondary" class="mr-1 w-20" data-tw-dismiss="modal">
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

    <!-- BEGIN: Import Modal -->
    @teleport('body')
        <x-base.dialog id="import-modal" wire:ignore.self>
            <x-base.dialog.panel class="p-0">
                <div class="p-5 border-b border-slate-200">
                    <h2 class="font-medium text-base text-slate-800 uppercase font-black">Importación Masiva de Productos
                    </h2>
                </div>
                <div class="p-5">
                    @if (!$importResults)
                        <div class="p-4 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200 text-center">
                            <x-base.lucide class="mx-auto h-12 w-12 text-slate-400" icon="UploadCloud" />
                            <div class="mt-2 text-slate-500 font-bold">Seleccione el archivo Excel (.xlsx)</div>
                            <input type="file" wire:model="excelFile" class="mt-4 w-full text-xs text-slate-800"
                                accept=".xlsx, .xls">
                            <div wire:loading wire:target="excelFile" class="mt-2 text-primary font-bold">Cargando
                                archivo...</div>
                        </div>

                        <div class="mt-4 p-3 bg-primary/10 rounded text-[11px] text-primary">
                            <strong>Instrucciones:</strong>
                            <ul class="list-disc ml-4 mt-1 font-bold">
                                <li>Use la <a href="javascript:;" wire:click="downloadTemplate"
                                        class="font-bold underline text-blue-700">plantilla oficial</a>.</li>
                                <li>No modifique los encabezados de la Hoja 1.</li>
                                <li>Consulte las Hojas 2 y 3 para ver ID de Categorías y Marcas.</li>
                            </ul>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="text-lg font-black text-success">PROCESO FINALIZADO</div>
                            <div class="mt-4 flex justify-center gap-4">
                                <div class="p-4 bg-success/10 rounded-xl border border-success/20 min-w-[100px]">
                                    <div class="text-3xl font-black text-success">{{ $importResults['success'] }}</div>
                                    <div class="text-[9px] uppercase font-black tracking-widest leading-tight">Exitosos
                                    </div>
                                </div>
                                <div class="p-4 bg-danger/10 rounded-xl border border-danger/20 min-w-[100px]">
                                    <div class="text-3xl font-black text-danger">{{ count($importResults['failed']) }}
                                    </div>
                                    <div class="text-[9px] uppercase font-black tracking-widest leading-tight">Fallidos
                                    </div>
                                </div>
                            </div>

                            @if (count($importResults['failed']) > 0)
                                <div class="mt-6 text-left">
                                    <h4
                                        class="font-black text-danger mb-3 text-xs border-b border-danger/10 pb-1 uppercase tracking-wider">
                                        Reporte de Errores:</h4>
                                    <div
                                        class="max-h-48 overflow-y-auto border border-slate-100 rounded-lg p-3 bg-slate-50 shadow-inner">
                                        @foreach ($importResults['failed'] as $error)
                                            <div
                                                class="mb-3 text-[11px] border-b border-slate-200 pb-2 last:border-0 last:mb-0">
                                                <div class="font-black text-slate-700 uppercase">Fila {{ $error['row'] }}:
                                                    <span class="text-primary italic">{{ $error['name'] }}</span></div>
                                                <ul class="text-danger mt-1 font-bold">
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
                <div class="p-5 border-t border-slate-200 flex justify-end gap-2 bg-slate-50">
                    <x-base.button variant="outline-secondary" class="font-bold" @click="$wire.showImportModal = false"
                        data-tw-dismiss="modal">
                        Cerrar
                    </x-base.button>
                    @if (!$importResults)
                        <x-base.button variant="primary" wire:click="importExcel" wire:loading.attr="disabled"
                            :disabled="!$excelFile" class="font-black">
                            <x-base.lucide icon="CheckCircle" class="w-4 h-4 mr-2" /> COMENZAR IMPORTACIÓN
                        </x-base.button>
                    @endif
                </div>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport
    <!-- END: Import Modal -->

    <!-- Delete Confirmation Modal -->
    @teleport('body')
        <x-base.dialog id="delete-confirmation-modal" wire:ignore.self>
            <x-base.dialog.panel>
                <div class="p-5 text-center">
                    <x-base.lucide class="mx-auto mt-3 h-16 w-16 text-danger" icon="XCircle" />
                    <div class="mt-5 text-3xl font-bold">¿Estás seguro?</div>
                    <div class="mt-2 text-slate-500 font-medium">
                        ¿Realmente deseas eliminar este producto? <br />
                        Esta acción no se puede deshacer.
                    </div>
                </div>
                <div class="px-5 pb-8 text-center bg-slate-50 pt-5 rounded-b-xl">
                    <x-base.button class="mr-2 w-24 font-bold" variant="outline-secondary" data-tw-dismiss="modal">
                        Cancelar
                    </x-base.button>
                    <x-base.button class="w-32 font-black" variant="danger" wire:click="delete">
                        SÍ, ELIMINAR
                    </x-base.button>
                </div>
            </x-base.dialog.panel>
        </x-base.dialog>
    @endteleport

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
