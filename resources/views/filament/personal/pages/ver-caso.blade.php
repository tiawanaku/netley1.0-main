<x-filament-panels::page>
    @php
        $proceso = $this->getProceso();
    @endphp

    <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h2 style="font-size: 1.125rem; font-weight: 600; margin: 0;">
                    {{ $proceso->materia_legal->getLabel() }} — {{ $proceso->tipo_proceso }}
                </h2>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0;">
                    {{ $proceso->cliente?->nombre_completo }} · Duración estimada {{ $proceso->tiempo_proceso_meses }} meses
                </p>
            </div>
            <x-filament::badge :color="$proceso->estado->getColor()">
                {{ $proceso->estado->getLabel() }}
            </x-filament::badge>
        </div>

        @if ($proceso->finanza)
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0.75rem 0 0;">
                Honorarios: Bs. {{ number_format($proceso->finanza->costo, 2) }} ({{ $proceso->finanza->tipo_pago->getLabel() }})
            </p>
        @endif

        <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
            {{ $this->contratoAction }}
        </div>
    </div>

    <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Repositorio de documentos</h3>
            {{ $this->subirDocumentoAction }}
        </div>

        @forelse ($proceso->documentos as $documento)
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem;">
                <div>
                    <div style="font-weight: 500;">{{ $documento->nombre }}</div>
                    <div style="font-size: 0.75rem; color: #6b7280;">
                        {{ $documento->categoria->getLabel() }} · {{ $documento->created_at->format('d/m/Y') }}
                        @if ($documento->origen->value === 'cliente')
                            · Subido por el cliente
                        @else
                            · Subido por {{ $documento->personal?->nombre_completo ?? 'Netley' }}
                        @endif
                    </div>
                </div>
                <a href="{{ $this->descargarDocumentoUrl($documento) }}" target="_blank" style="text-decoration: none;">
                    <x-filament::button size="sm" outlined icon="heroicon-o-arrow-down-tray">
                        Descargar
                    </x-filament::button>
                </a>
            </div>
        @empty
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Todavía no hay documentos en el expediente.</p>
        @endforelse
    </div>
</x-filament-panels::page>
