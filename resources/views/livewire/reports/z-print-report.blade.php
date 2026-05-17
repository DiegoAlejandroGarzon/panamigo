<div class="px-5 pb-8">
    <div class="intro-y flex items-center mt-8 mb-6">
        <h2 class="text-2xl font-black mr-auto tracking-tight uppercase text-slate-800">
            <span class="text-primary">🧾</span> Reporte Mensual de Z Físicos
        </h2>
    </div>

    @if (session()->has('z_message'))
        <div class="intro-y mb-4">
            <x-base.alert variant="soft-success" class="flex items-center shadow-sm border-l-4 border-success">
                <x-base.lucide icon="CheckCircle" class="w-5 h-5 mr-2" />
                {{ session('z_message') }}
            </x-base.alert>
        </div>
    @endif

    <!-- Filtros -->
    <div class="intro-y box p-5 mb-6 rounded-2xl">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="form-label text-xs font-bold uppercase text-slate-500">Año</label>
                <select wire:model.live="selectedYear" class="form-select w-32">
                    @for ($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label text-xs font-bold uppercase text-slate-500">Mes</label>
                <select wire:model.live="selectedMonth" class="form-select w-40">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}">{{ Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Resumen comparativo -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="intro-y box p-5 rounded-2xl">
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                <x-base.lucide icon="Printer" class="w-5 h-5 text-primary" />
            </div>
            <div class="text-2xl font-black tracking-tighter text-slate-800">
                ${{ number_format($totalReported, 0, ',', '.') }}</div>
            <div class="text-xs font-bold text-slate-400 uppercase mt-1">Total Reportado (Z Físico)</div>
            <div class="text-xs text-slate-400 mt-1">{{ $zPrints->count() }} cierres · {{ number_format($totalCount) }} ventas</div>
        </div>

        <div class="intro-y box p-5 rounded-2xl">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                <x-base.lucide icon="Database" class="w-5 h-5 text-slate-500" />
            </div>
            <div class="text-2xl font-black tracking-tighter text-slate-800">
                ${{ number_format($realTotal, 0, ',', '.') }}</div>
            <div class="text-xs font-bold text-slate-400 uppercase mt-1">Total Real (Base de Datos)</div>
            <div class="text-xs text-slate-400 mt-1">{{ number_format($realCount) }} ventas registradas</div>
        </div>

        <div class="intro-y box p-5 rounded-2xl border-l-4 {{ $difference >= 0 ? 'border-success' : 'border-danger' }}">
            <div class="w-10 h-10 rounded-full {{ $difference >= 0 ? 'bg-success/10' : 'bg-danger/10' }} flex items-center justify-center mb-3">
                <x-base.lucide icon="{{ $difference >= 0 ? 'TrendingUp' : 'TrendingDown' }}" class="w-5 h-5 {{ $difference >= 0 ? 'text-success' : 'text-danger' }}" />
            </div>
            <div class="text-2xl font-black tracking-tighter {{ $difference >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $difference >= 0 ? '+' : '-' }}${{ number_format(abs($difference), 0, ',', '.') }}</div>
            <div class="text-xs font-bold text-slate-400 uppercase mt-1">Diferencia (Físico − BD)</div>
            <div class="text-xs text-slate-400 mt-1">
                {{ $difference == 0 ? 'Sin diferencia' : ($difference > 0 ? 'Reportado más de lo registrado' : 'Reportado menos de lo registrado') }}
            </div>
        </div>

    </div>

    <!-- Tabla de Z impresos -->
    <div class="intro-y box rounded-2xl overflow-hidden">
        <div class="flex items-center px-5 py-4 border-b border-slate-100">
            <x-base.lucide icon="FileText" class="w-5 h-5 text-primary mr-2" />
            <h3 class="font-bold text-slate-700">Detalle de Z Físicos Impresos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-hover w-full">
                <thead>
                    <tr class="bg-slate-50 text-xs font-black uppercase text-slate-400">
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Fecha</th>
                        <th class="px-5 py-3 text-right">Total Reportado</th>
                        <th class="px-5 py-3 text-right">Ventas Reportadas</th>
                        <th class="px-5 py-3 text-left">Cajero</th>
                        <th class="px-5 py-3 text-left">Hora de Impresión</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zPrints as $i => $zp)
                        <tr class="border-b border-slate-50">
                            <td class="px-5 py-3 text-xs font-bold text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-bold text-slate-700">
                                {{ $zp->print_date->translatedFormat('d \d\e F') }}</td>
                            <td class="px-5 py-3 text-right font-black text-slate-800">
                                ${{ number_format($zp->reported_total, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-600">
                                {{ number_format($zp->reported_count) }}</td>
                            <td class="px-5 py-3 text-slate-500 text-sm">
                                {{ $zp->user->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-400 text-xs">
                                {{ $zp->created_at->format('h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-300">
                                <x-base.lucide icon="Printer" class="w-10 h-10 mx-auto mb-2 opacity-20" />
                                <p class="font-bold text-xs uppercase tracking-widest">No hay Z impresos en este período</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($zPrints->isNotEmpty())
                    <tfoot>
                        <tr class="bg-slate-50 font-black text-slate-700">
                            <td colspan="2" class="px-5 py-3 text-xs uppercase">TOTAL DEL MES</td>
                            <td class="px-5 py-3 text-right">${{ number_format($totalReported, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right">{{ number_format($totalCount) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
