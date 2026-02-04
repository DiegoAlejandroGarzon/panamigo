<div class="intro-y box p-5 mt-5">
    <div class="flex flex-col sm:flex-row items-center border-b border-slate-200/60 p-5 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Gestión de Productos</h2>
        <button wire:click="create()" class="btn btn-primary shadow-md mr-2">Nuevo Producto</button>
    </div>
    
    @if (session()->has('message'))
        <div class="alert alert-success show mb-2 mt-2" role="alert">{{ session('message') }}</div>
    @endif

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto p-5 dark:bg-darkmode-600">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-darkmode-400">
                    <h3 class="text-lg font-medium">{{ $product_id ? 'Editar Producto' : 'Crear Producto' }}</h3>
                    <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>
                <div class="mt-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Nombre</label>
                        <input type="text" wire:model="name" class="form-control w-full">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Precio</label>
                        <input type="text" wire:model="price" class="form-control w-full">
                        @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Stock</label>
                        <input type="number" wire:model="stock" class="form-control w-full">
                        @error('stock') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-darkmode-400">
                    <button wire:click="closeModal()" class="btn btn-secondary mr-2">Cancelar</button>
                    <button wire:click="store()" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-x-auto mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">ID</th>
                    <th class="whitespace-nowrap">NOMBRE</th>
                    <th class="whitespace-nowrap">PRECIO</th>
                    <th class="whitespace-nowrap">STOCK</th>
                    <th class="whitespace-nowrap">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>$ {{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <button wire:click="edit({{ $product->id }})" class="btn btn-warning btn-sm mr-1">Editar</button>
                        <button wire:click="delete({{ $product->id }})" class="btn btn-danger btn-sm">Borrar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
