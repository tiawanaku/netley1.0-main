<x-filament-panels::page>
    @php
        $cliente = $this->getCliente();
        $abogado = $this->getAbogadoPatrocinante();
        $proximaCita = $this->getProximaCita();
        $proximoPago = $this->getProximoPago();
        $notificaciones = $this->getNotificacionesRecientes();
    @endphp

    <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h2 style="font-size: 1.125rem; font-weight: 600; margin: 0;">
                    Hola, {{ $cliente->nombre }} 👋
                </h2>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0;">
                    Aquí puedes revisar el estado de tus procesos, comunicarte con tu abogado y gestionar tus pagos.
                </p>
            </div>

            {{ $this->fichaClienteAction }}
        </div>
    </div>

    <div style="margin-top: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Abogado patrocinante</div>
            <div style="font-size: 1rem; font-weight: 600; margin-top: 0.375rem;">
                {{ $abogado?->nombre_completo ?? 'Aún no asignado' }}
            </div>
        </div>

        <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Procesos activos</div>
            <div style="font-size: 1rem; font-weight: 600; margin-top: 0.375rem;">
                {{ $this->getProcesosActivosCount() }}
            </div>
        </div>

        <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Próxima cita</div>
            <div style="font-size: 1rem; font-weight: 600; margin-top: 0.375rem;">
                @if ($proximaCita)
                    {{ $proximaCita->fecha_inicio->format('d/m/Y H:i') }}
                @else
                    Sin citas programadas
                @endif
            </div>
        </div>

        <div style="border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em;">Próximo pago</div>
            <div style="font-size: 1rem; font-weight: 600; margin-top: 0.375rem;">
                @if ($proximoPago)
                    Bs. {{ number_format($proximoPago->monto, 2) }} — {{ $proximoPago->fecha->format('d/m/Y') }}
                @else
                    Sin cuotas pendientes
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">
                Mis procesos
            </h3>
            <x-filament::button size="sm" outlined tag="a" :href="\App\Filament\Cliente\Pages\MisProcesos::getUrl()">
                Ver todos
            </x-filament::button>
        </div>

        @forelse ($cliente->procesos as $proceso)
            <a
                href="{{ \App\Filament\Cliente\Pages\MiProceso::getUrl(['proceso' => $proceso->id]) }}"
                style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6; text-decoration: none; color: inherit;"
            >
                <div>
                    <div style="font-size: 0.875rem; font-weight: 500;">
                        {{ $proceso->materia_legal->getLabel() }} — {{ $proceso->tipo_proceso }}
                    </div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                        {{ $proceso->abogado?->nombre_completo ?? 'Abogado por asignar' }}
                    </div>
                </div>
                <x-filament::badge :color="$proceso->estado->getColor()">
                    {{ $proceso->estado->getLabel() }}
                </x-filament::badge>
            </a>
        @empty
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                Todavía no tienes procesos registrados.
            </p>
        @endforelse
    </div>

    <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 1rem;">
            Notificaciones recientes
        </h3>

        @forelse ($notificaciones as $notificacion)
            <div style="padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem;">
                <div style="font-weight: 500;">{{ $notificacion->data['title'] ?? 'Notificación' }}</div>
                @if (! empty($notificacion->data['body']))
                    <div style="color: #6b7280;">{{ $notificacion->data['body'] }}</div>
                @endif
                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                    {{ $notificacion->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                No tienes notificaciones recientes.
            </p>
        @endforelse
    </div>
</x-filament-panels::page>
