<div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Admin Dashboard - Reportes
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <h3 class="text-xl font-bold mb-4">Pedidos por Empleado</h3>
            <table class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">EMPLEADO</th>
                        <th class="whitespace-nowrap">ROL</th>
                        <th class="text-center whitespace-nowrap">TOTAL PEDIDOS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr class="intro-x">
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $employee->name }}</div>
                                <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $employee->email }}
                                </div>
                            </td>
                            <td>
                                @foreach ($employee->roles as $role)
                                    <span
                                        class="px-2 py-1 rounded-full bg-theme-1 text-white text-xs mr-1">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">{{ $employee->orders_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
