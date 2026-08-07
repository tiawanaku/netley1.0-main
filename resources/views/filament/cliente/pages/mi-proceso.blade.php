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
                    {{ $proceso->abogado?->nombre_completo ?? 'Abogado por asignar' }} · Duración estimada {{ $proceso->tiempo_proceso_meses }} meses
                </p>
            </div>
            <x-filament::badge :color="$proceso->estado->getColor()">
                {{ $proceso->estado->getLabel() }}
            </x-filament::badge>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
            {{ $this->contratoAction }}
            {{ $this->planPagosAction }}
            {{ $this->fichaProcesoAction }}
        </div>
    </div>

    {{-- Expediente digital --}}
    <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Expediente digital</h3>
            {{ $this->subirDocumentoAction }}
        </div>

        @php
            $solicitudesPendientes = $proceso->solicitudesDocumento->where('estado', \App\Enums\EstadoSolicitudDocumento::Pendiente);
        @endphp

        @if ($solicitudesPendientes->isNotEmpty())
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.5rem;">
                    Documentos solicitados por tu abogado
                </div>
                @foreach ($solicitudesPendientes as $solicitud)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem;">
                        <div>
                            <div>{{ $solicitud->descripcion }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Solicitado por {{ $solicitud->personal?->nombre_completo }}</div>
                        </div>
                        <x-filament::button
                            size="sm"
                            outlined
                            wire:click="mountAction('subirDocumento', { solicitud: {{ $solicitud->id }} })"
                        >
                            Subir
                        </x-filament::button>
                    </div>
                @endforeach
            </div>
        @endif

        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.5rem;">
            Documentos ({{ $proceso->documentos->count() }})
        </div>

        @forelse ($proceso->documentos as $documento)
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem;">
                <div>
                    <div style="font-weight: 500;">{{ $documento->nombre }}</div>
                    <div style="font-size: 0.75rem; color: #6b7280;">
                        {{ $documento->categoria->getLabel() }} · {{ $documento->created_at->format('d/m/Y') }}
                        @if ($documento->origen->value === 'cliente')
                            · Subido por ti
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
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Todavía no hay documentos en tu expediente.</p>
        @endforelse
    </div>

    {{-- Finanzas / pagos --}}
    @if ($proceso->finanza)
        <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 1rem;">Plan de pagos</h3>

            @foreach ($proceso->finanza->planPagos as $cuota)
                <div style="padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500;">Bs. {{ number_format($cuota->monto, 2) }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Vence {{ $cuota->fecha->format('d/m/Y') }}</div>
                        </div>
                        <x-filament::badge :color="$cuota->estado->getColor()">
                            {{ $cuota->estado->getLabel() }}
                        </x-filament::badge>
                    </div>

                    @if (in_array($cuota->estado, [\App\Enums\EstadoPago::Pendiente, \App\Enums\EstadoPago::Vencido]))
                        <div style="margin-top: 0.75rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <img src="{{ $this->qrUrlPara($cuota) }}" alt="QR de pago" style="width: 96px; height: 96px; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                            <div style="font-size: 0.75rem; color: #6b7280; max-width: 20rem;">
                                Escanea el código QR con tu app de pagos y luego sube tu comprobante para que el equipo de Netley confirme el pago.
                            </div>
                            <x-filament::button
                                size="sm"
                                outlined
                                wire:click="mountAction('subirComprobante', { planPago: {{ $cuota->id }} })"
                            >
                                Ya pagué, subir comprobante
                            </x-filament::button>
                        </div>
                    @elseif ($cuota->estado === \App\Enums\EstadoPago::PendienteConfirmacion)
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #6b7280;">
                            Comprobante enviado el {{ $cuota->pagado_en?->format('d/m/Y H:i') }}. Esperando confirmación de Netley.
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Línea de tiempo --}}
    <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 1rem;">
            Línea de tiempo del caso
        </h3>

        <ol style="list-style: none; margin: 0; padding: 0; border-left: 2px solid #e5e7eb; margin-left: 0.5rem;">
            @foreach ($proceso->timeline() as $evento)
                <li style="margin-left: 1rem; padding: 0 0 1.5rem 1rem; position: relative;">
                    <span style="position: absolute; left: -1.4rem; top: 0.35rem; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #2563eb;"></span>
                    <time style="font-size: 0.75rem; color: #6b7280;">{{ $evento['fecha']->format('d/m/Y') }}</time>
                    <p style="font-size: 0.875rem; font-weight: 500; margin: 0.125rem 0 0;">{{ $evento['titulo'] }}</p>
                    @if ($evento['descripcion'])
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.125rem 0 0;">{{ $evento['descripcion'] }}</p>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</x-filament-panels::page>
