<div class="p-5">
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto flex items-center">
            <x-base.lucide icon="LayoutDashboard" class="w-6 h-6 mr-2 text-primary" /> Panel de Control Administrativo
        </h2>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex">
                        <x-base.lucide icon="DollarSign" class="report-box__icon text-success" />
                        <div class="ml-auto">
                            <div class="report-box__indicator bg-success cursor-pointer" title="Ventas hoy"> Hoy </div>
                        </div>
                    </div>
                    <div class="text-3xl font-medium leading-8 mt-6">${{ number_format($totalSalesToday, 2) }}</div>
                    <div class="text-base text-slate-500 mt-1">Ventas de Hoy</div>
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex">
                        <x-base.lucide icon="ShoppingCart" class="report-box__icon text-warning" />
                        <div class="ml-auto">
                            <div class="report-box__indicator bg-warning cursor-pointer" title="Pedidos totales"> Total
                            </div>
                        </div>
                    </div>
                    <div class="text-3xl font-medium leading-8 mt-6">{{ $totalOrdersToday }}</div>
                    <div class="text-base text-slate-500 mt-1">Pedidos de Hoy</div>
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex">
                        <x-base.lucide icon="Clock" class="report-box__icon text-info" />
                    </div>
                    <div class="text-3xl font-medium leading-8 mt-6">{{ $pendingOrdersCount }}</div>
                    <div class="text-base text-slate-500 mt-1">Pedidos por Cobrar</div>
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex">
                        <x-base.lucide icon="User" class="report-box__icon text-primary" />
                    </div>
                    <div class="text-3xl font-medium leading-8 mt-6">{{ $employees->count() }}</div>
                    <div class="text-base text-slate-500 mt-1">Personal Activo</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Sales Performance Table -->
        <div class="col-span-12 xl:col-span-8 mt-6">
            <div class="intro-y block sm:flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Ventas Recientes</h2>
            </div>
            <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
                <table class="table table-report sm:mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">ID</th>
                            <th class="whitespace-nowrap">MESERO / CAJERO</th>
                            <th class="text-center whitespace-nowrap">ESTADO</th>
                            <th class="text-center whitespace-nowrap">TOTAL</th>
                            <th class="text-center whitespace-nowrap">FECHA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr class="intro-x">
                                <td class="w-20">#{{ $order->id }}</td>
                                <td>
                                    <div class="font-medium whitespace-nowrap">{{ $order->user->name ?? 'Sistema' }}
                                    </div>
                                    <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                        {{ $order->customer_served_by }}</div>
                                </td>
                                <td class="text-center">
                                    <div
                                        class="flex items-center justify-center {{ $order->status == 'paid' ? 'text-success' : 'text-warning' }}">
                                        <x-base.lucide icon="{{ $order->status == 'paid' ? 'CheckCircle' : 'Clock' }}"
                                            class="w-4 h-4 mr-2" />
                                        {{ strtoupper($order->status) }}
                                    </div>
                                </td>
                                <td class="text-center font-bold text-slate-700">${{ number_format($order->total, 2) }}
                                </td>
                                <td class="text-center text-slate-500 text-xs">{{ $order->created_at->format('H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products & Performance -->
        <div class="col-span-12 xl:col-span-4 mt-6">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Productos Más Vendidos</h2>
            </div>
            <div class="intro-y box p-5 mt-5">
                @foreach ($topProducts as $top)
                    <div class="flex items-center mt-4 first:mt-0">
                        <div
                            class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden bg-slate-100 flex items-center justify-center">
                            <x-base.lucide icon="Package" class="w-5 h-5 text-slate-400" />
                        </div>
                        <div class="ml-4 mr-auto">
                            <div class="font-medium">{{ $top->product->name ?? 'Producto Borrado' }}</div>
                            <div class="text-slate-500 text-xs mt-0.5">{{ $top->total_qty }} unidades</div>
                        </div>
                        <div class="font-medium text-slate-700">#{{ $loop->iteration }}</div>
                    </div>
                @endforeach

                <h2 class="text-lg font-medium truncate mt-8 mb-4 border-t pt-4">Rendimiento Hoy</h2>
                @foreach ($employees as $employee)
                    <div class="flex items-center mt-3">
                        <div
                            class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                            {{ substr($employee->name, 0, 1) }}
                        </div>
                        <div class="ml-3 mr-auto">
                            <div class="font-medium text-sm">{{ $employee->name }}</div>
                        </div>
                        <div class="text-slate-500 font-medium">{{ $employee->orders_count }} pedidos</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
