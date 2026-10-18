<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white leading-tight">Calendario de la sede</h2>
            @if ($esDirigente)
                <a href="{{ route('reservas.index', ['semana' => $lunes->toDateString()]) }}" class="ui-button ui-button-primary">Gestionar reservas</a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Navegación entre semanas --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Semana del {{ $dias->first()->translatedFormat('j \d\e F') }}
                    al {{ $dias->last()->translatedFormat('j \d\e F \d\e Y') }}
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('calendario.index', ['semana' => $lunes->copy()->subWeek()->toDateString()]) }}" class="ui-button ui-button-secondary py-2">← Anterior</a>
                    <a href="{{ route('calendario.index') }}" class="ui-button ui-button-secondary py-2">Esta semana</a>
                    <a href="{{ route('calendario.index', ['semana' => $lunes->copy()->addWeek()->toDateString()]) }}" class="ui-button ui-button-secondary py-2">Siguiente →</a>
                </div>
            </div>

            {{-- Grilla semanal --}}
            @if ($esDirigente)
                <details class="ui-panel group">
                    <summary class="flex cursor-pointer items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Imprimir horario
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </summary>
                    <form method="GET" action="{{ route('calendario.imprimir') }}" target="_blank" class="flex flex-wrap items-end gap-3 border-t border-gray-200 p-4 dark:border-gray-800">
                        <div>
                            <x-input-label for="fecha_impresion" value="Fecha del período" />
                            <x-date-input id="fecha_impresion" name="fecha" :value="$lunes->toDateString()" required />
                        </div>
                        <div>
                            <x-input-label for="periodo_impresion" value="Imprimir" />
                            <select id="periodo_impresion" name="periodo" class="ui-input w-full sm:min-w-72" required>
                                <option value="semana">Semana de la fecha elegida</option>
                                <option value="mes">Mes de la fecha elegida</option>
                            </select>
                        </div>
                        <x-primary-button>Ver horario imprimible</x-primary-button>
                    </form>
                </details>
            @endif
            @php
                // Une bloques consecutivos iguales de un mismo día en una sola celda (rowspan).
                $filas = [];
                $cubiertas = [];
                foreach ($dias as $dia) {
                    $f = $dia->toDateString();
                    $inicio = null;
                    foreach ($horas as $hora) {
                        $celda = $celdas[$f][$hora] ?? null;
                        if ($celda && $inicio && $celda == $celdas[$f][$inicio]) {
                            $filas[$f][$inicio]++;
                            $cubiertas[$f][$hora] = true;
                        } else {
                            $inicio = $celda ? $hora : null;
                            if ($celda) {
                            $filas[$f][$hora] = 1;
                            }
                        }
                    }
                }
            @endphp

    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-gray-500 dark:text-gray-400">
        @if ($esDirigente)
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-brand-500"></span>Reserva</span>
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-amber-500"></span>Horario fijo de grupo</span>
        @else
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-gray-400"></span>Ocupado</span>
        @endif
        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm border border-gray-300 dark:border-gray-600"></span>Libre</span>
    </div>

    <div class="ui-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[56rem] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th class="w-20"></th>
                        @foreach ($dias as $dia)
                            <th class="border-l border-gray-200 px-3 py-3 text-center dark:border-gray-800 {{ $dia->isToday() ? 'bg-brand-50 dark:bg-brand-500/10' : '' }}">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $dia->translatedFormat('D') }}</div>
                                <div class="mt-0.5 text-lg font-semibold {{ $dia->isToday() ? 'text-brand-500 dark:text-brand-400' : 'text-gray-900 dark:text-white' }}">{{ $dia->format('j') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($horas as $hora)
                        <tr class="border-t border-gray-200 dark:border-gray-800">
                            <td class="h-12 pr-4 pt-2 text-right align-top text-xs font-medium text-gray-500 dark:text-gray-400">{{ $hora }}</td>
                            @foreach ($dias as $dia)
                                @php $f = $dia->toDateString(); @endphp
                                @continue(isset($cubiertas[$f][$hora]))
                                @php $celda = $celdas[$f][$hora] ?? null; @endphp

                                @if (! $celda)
                                    <td class="h-12 border-l border-gray-200 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5 {{ $dia->isToday() ? 'bg-brand-50/40 dark:bg-brand-500/5' : '' }}"></td>
                                @else
                                    @php
                                        $clase = ! $esDirigente
                                            ? 'border-gray-400 bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-200'
                                            : ($celda['tipo'] === 'fijo'
                                                ? 'border-amber-500 bg-amber-50 text-amber-900 dark:bg-amber-500/10 dark:text-amber-200'
                                                : 'border-brand-500 bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400');
                                    @endphp
                                    <td rowspan="{{ $filas[$f][$hora] }}" class="border-l-4 px-3 py-2 align-top text-xs font-medium leading-snug {{ $clase }}">
                                        {{ $celda['texto'] }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
