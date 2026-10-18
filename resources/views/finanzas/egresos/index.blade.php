<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white leading-tight">Egresos</h2>
            <a href="{{ route('egresos.create') }}" class="ui-button ui-button-primary">Registrar egreso</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status') === 'egreso-registrado')
                <div class="ui-alert ui-alert-success">Egreso registrado.</div>
            @endif
            @if (session('status') === 'egreso-anulado')
                <div class="ui-alert ui-alert-success">Egreso anulado.</div>
            @endif
            @error('egreso')
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
                            <th>N° transacción</th>
                            <th>Pagó</th>
                            <th>Comprobante</th>
                            <th>Registrado por</th>
                            <th><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($egresos as $egreso)
                            <tr class="align-top {{ $egreso->estaAnulado() ? 'opacity-60' : '' }}">
                                <td class="whitespace-nowrap">{{ $egreso->fecha_pago->format('d/m/Y') }}</td>
                                <td>
                                    {{ \App\Models\Egreso::CONCEPTOS[$egreso->concepto] ?? $egreso->concepto }}
                                    @if ($egreso->estaAnulado())
                                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">
                                            Anulado el {{ $egreso->anulado_en->format('d/m/Y') }}: {{ $egreso->motivo_anulacion }}
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-right font-medium tabular-nums">${{ number_format($egreso->monto, 0, ',', '.') }}</td>
                                <td>{{ $egreso->numero_transaccion }}</td>
                                <td>{{ $egreso->pagado_por }}</td>
                                <td>
                                    <a href="{{ route('egresos.comprobante', $egreso) }}" target="_blank" class="ui-link">Ver</a>
                                </td>
                                <td>{{ $egreso->registrador?->name ?? '—' }}</td>
                                <td>
                                    <div class="flex flex-col items-end gap-2">
                                        @unless ($egreso->estaAnulado())
                                            <details>
                                                <summary class="ui-action ui-action-danger min-w-24 justify-center list-none [&::-webkit-details-marker]:hidden">Anular</summary>
                                                <form method="POST" action="{{ route('egresos.anular', $egreso) }}" class="mt-2 w-56 space-y-2">
                                                    @csrf
                                                    <textarea name="motivo_anulacion" rows="2" required placeholder="Motivo obligatorio" class="ui-input block w-full"></textarea>
                                                    <button type="submit" class="ui-action border-transparent bg-red-600 text-white hover:bg-red-700">Confirmar anulación</button>
                                                </form>
                                            </details>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-8 text-center text-gray-500 dark:text-gray-400">Sin egresos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $egresos->links() }}
        </div>
    </div>
</x-app-layout>