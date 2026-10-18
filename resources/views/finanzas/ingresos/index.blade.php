<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white leading-tight">Ingresos</h2>
            <a href="{{ route('ingresos.create') }}" class="ui-button ui-button-primary">Registrar ingreso</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status') === 'ingreso-anulado')
                <div class="ui-alert ui-alert-success">Ingreso anulado.</div>
            @endif
            @if (session('status') === 'devolucion-registrada')
                <div class="ui-alert ui-alert-success">Devolución registrada.</div>
            @endif
            @error('ingreso')
                <div class="ui-alert ui-alert-error">{{ $message }}</div>
            @enderror
            @error('devolucion')
                <div class="ui-alert ui-alert-error">{{ $message }}</div>
            @enderror

            <div class="ui-panel p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Saldo de caja</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums {{ $saldo < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $saldo < 0 ? '-' : '' }}${{ number_format(abs($saldo), 0, ',', '.') }}
                </p>
            </div>

            <div class="ui-panel overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th class="text-right">Monto</th>
                            <th>Medio</th>
                            <th>Comprobante</th>
                            <th>Registrado por</th>
                            <th><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ingresos as $ingreso)
                            <tr class="align-top {{ $ingreso->estaAnulado() ? 'opacity-60' : '' }}">
                                <td class="whitespace-nowrap">{{ $ingreso->fecha_pago->format('d/m/Y') }}</td>
                                <td>
                                    {{ \App\Models\Ingreso::CONCEPTOS[$ingreso->concepto] ?? $ingreso->concepto }}
                                    @if ($ingreso->tipo_cuota)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ \App\Models\Ingreso::TIPOS_CUOTA[$ingreso->tipo_cuota] }})</span>
                                    @endif
                                    @if ($ingreso->estaAnulado())
                                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">
                                            Anulado el {{ $ingreso->anulado_en->format('d/m/Y') }}: {{ $ingreso->motivo_anulacion }}
                                        </div>
                                    @endif
                                    @if ($ingreso->devolucion)
                                        <div class="mt-1 text-xs text-amber-700 dark:text-amber-400">
                                            Devuelto el {{ $ingreso->devolucion->fecha_devolucion->format('d/m/Y') }}: {{ $ingreso->devolucion->motivo }}
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-right font-medium tabular-nums">${{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                                <td>{{ \App\Models\Ingreso::MEDIOS[$ingreso->medio_pago] }}</td>
                                <td>
                                    {{ $ingreso->talonario ?? $ingreso->numero_transaccion }}
                                    @php($solicitudInsumoId = $ingreso->solicitud_insumo_id ?? $ingreso->garantia?->solicitud_insumo_id)
                                    @if ($solicitudInsumoId)
                                        <a class="ui-link block" href="{{ route('finanzas.insumos.show', $solicitudInsumoId) }}">Préstamo y comprobante</a>
                                    @elseif ($ingreso->reserva_id || $ingreso->garantia)
                                        <a class="ui-link block" href="{{ route('arriendos.show', ['reserva' => $ingreso->reserva_id ?? $ingreso->garantia->reserva_id, 'desde' => 'finanzas']) }}">Reserva y comprobante</a>
                                    @endif
                                </td>
                                <td>{{ $ingreso->registrador?->name ?? '—' }}</td>
                                <td>
                                    <div class="flex flex-col items-end gap-2">
                                        @unless ($ingreso->estaAnulado() || $ingreso->devolucion)
                                            <details>
                                                <summary class="ui-action ui-action-danger min-w-19 justify-center list-none [&::-webkit-details-marker]:hidden">Anular</summary>
                                                <form method="POST" action="{{ route('ingresos.anular', $ingreso) }}" class="mt-2 w-56 space-y-2">
                                                    @csrf
                                                    <textarea name="motivo_anulacion" rows="2" required placeholder="Motivo obligatorio" class="ui-input block w-full"></textarea>
                                                    <button type="submit" class="ui-action border-transparent bg-red-600 text-white hover:bg-red-700">Confirmar anulación</button>
                                                </form>
                                            </details>
                                        @endunless

                                        @if ($ingreso->puedeDevolverse())
                                            <details>
                                                <summary class="ui-action ui-action-warning list-none [&::-webkit-details-marker]:hidden">Devolver</summary>
                                                <form method="POST" action="{{ route('ingresos.devolver', $ingreso) }}" class="mt-2 w-56 space-y-2">
                                                    @csrf
                                                    <textarea name="motivo" rows="2" required placeholder="Motivo obligatorio" class="ui-input block w-full"></textarea>
                                                    <button type="submit" class="ui-action border-transparent bg-amber-600 text-white hover:bg-amber-700">Confirmar devolución</button>
                                                </form>
                                            </details>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">Sin ingresos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $ingresos->links() }}
        </div>
    </div>
</x-app-layout>