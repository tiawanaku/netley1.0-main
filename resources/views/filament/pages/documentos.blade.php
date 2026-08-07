<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">
            Documentos disponibles
        </h3>

        <div class="flex flex-wrap gap-3">
            {{ $this->contratoAction }}
            {{ $this->planPagosAction }}
            {{ $this->aceptacionAction }}
            {{ $this->fichaClienteAction }}
            {{ $this->fichaProcesoAction }}
        </div>
    </div>
</x-filament-panels::page>
