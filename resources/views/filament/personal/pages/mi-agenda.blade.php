<x-filament-panels::page>
    @php
        $agendas = $this->getAgendas();
        $casos = $this->getCasos();
    @endphp

    <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 1rem;">Mi agenda</h3>

        @forelse ($agendas as $agenda)
            @php
                $consulta = $agenda->consulta;
                $tieneCliente = $consulta?->cliente()->exists() ?? false;
            @endphp
            <div style="padding: 0.9rem 0; border-bottom: 1px solid #f3f4f6;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-filament::badge :color="$agenda->tipo->getColor()">{{ $agenda->tipo->getLabel() }}</x-filament::badge>
                            <x-filament::badge :color="$agenda->estado->getColor()">{{ $agenda->estado->getLabel() }}</x-filament::badge>
                        </div>
                        <p style="font-size: 0.95rem; font-weight: 500; margin: 0.4rem 0 0;">
                            {{ $agenda->relacionado_con ?? 'Sin referencia' }}
                        </p>
                        <p style="font-size: 0.8rem; color: #6b7280; margin: 0.15rem 0 0;">
                            {{ $agenda->fecha_inicio->format('d/m/Y H:i') }} – {{ $agenda->fecha_fin->format('H:i') }}
                            @if ($agenda->modalidad)
                                · {{ $agenda->modalidad->getLabel() }}
                            @endif
                        </p>
                        @if ($agenda->descripcion)
                            <p style="font-size: 0.8rem; color: #6b7280; margin: 0.15rem 0 0;">{{ $agenda->descripcion }}</p>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        @if (! in_array($agenda->estado, [\App\Enums\EstadoAgenda::Finalizada, \App\Enums\EstadoAgenda::Cancelada]))
                            <x-filament::button
                                size="sm"
                                color="success"
                                outlined
                                wire:click="mountAction('cierre', { agenda: {{ $agenda->id }} })"
                            >
                                Cierre
                            </x-filament::button>
                        @endif

                        @if ($agenda->tipo === \App\Enums\TipoAgenda::Cita && $consulta && $agenda->estado === \App\Enums\EstadoAgenda::Finalizada && ! $tieneCliente)
                            <x-filament::button
                                size="sm"
                                color="success"
                                wire:click="mountAction('registrarPago', { agenda: {{ $agenda->id }} })"
                            >
                                Registrar pago y crear Cliente Ejecutivo
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">No tienes actividades agendadas.</p>
        @endforelse
    </div>

    <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 1rem;">Mis casos</h3>

        @forelse ($casos as $caso)
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f3f4f6; gap: 0.5rem; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 0.875rem; font-weight: 500;">{{ $caso->cliente?->nombre_completo }} — {{ $caso->tipo_proceso }}</div>
                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $caso->materia_legal->getLabel() }} · {{ $caso->tiempo_proceso_meses }} meses</div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <x-filament::badge :color="$caso->estado->getColor()">{{ $caso->estado->getLabel() }}</x-filament::badge>
                    <a href="{{ \App\Filament\Personal\Pages\VerCaso::getUrl(['proceso' => $caso]) }}" style="text-decoration: none;">
                        <x-filament::button size="sm" outlined>
                            Ver caso
                        </x-filament::button>
                    </a>
                </div>
            </div>
        @empty
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Todavía no tienes casos asignados.</p>
        @endforelse
    </div>
</x-filament-panels::page>
