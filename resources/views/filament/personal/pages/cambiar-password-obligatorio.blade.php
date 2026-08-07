<x-filament-panels::page>
    <div style="max-width: 32rem; margin: 0 auto; border-radius: 0.75rem; background: var(--fi-panel-bg, #fff); padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); border: 1px solid rgba(0,0,0,.05);">
        <p style="font-size: 0.9rem; color: #6b7280; margin: 0 0 1.25rem;">
            Por seguridad, debes cambiar tu contraseña temporal antes de continuar. No podrás acceder a ningún módulo hasta hacerlo.
        </p>

        <form wire:submit.prevent>
            {{ $this->form }}

            <div style="margin-top: 1.25rem;">
                {{ $this->guardarAction }}
            </div>
        </form>
    </div>
</x-filament-panels::page>
