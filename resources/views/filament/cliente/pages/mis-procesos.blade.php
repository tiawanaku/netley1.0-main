<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($this->getProcesos() as $proceso)
            <a
                href="{{ \App\Filament\Cliente\Pages\MiProceso::getUrl(['proceso' => $proceso->id]) }}"
                style="display: block; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05); text-decoration: none; color: inherit;"
            >
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">
                            {{ $proceso->materia_legal->getLabel() }} — {{ $proceso->tipo_proceso }}
                        </h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0;">
                            {{ $proceso->abogado?->nombre_completo ?? 'Abogado por asignar' }} · Duración estimada {{ $proceso->tiempo_proceso_meses }} meses
                        </p>
                    </div>
                    <x-filament::badge :color="$proceso->estado->getColor()">
                        {{ $proceso->estado->getLabel() }}
                    </x-filament::badge>
                </div>

                @if ($proceso->finanza)
                    <div style="margin-top: 1rem; display: flex; gap: 2rem; flex-wrap: wrap; font-size: 0.875rem;">
                        <div>
                            <div style="color: #6b7280;">Costo total</div>
                            <div style="font-weight: 500;">Bs. {{ number_format($proceso->finanza->costo, 2) }}</div>
                        </div>
                        <div>
                            <div style="color: #6b7280;">Cuotas pendientes</div>
                            <div style="font-weight: 500;">{{ $proceso->finanza->planPagos->whereNotIn('estado', [\App\Enums\EstadoPago::Pagado])->count() }}</div>
                        </div>
                    </div>
                @endif
            </a>
        @empty
            <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                    Todavía no tienes procesos registrados.
                </p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
