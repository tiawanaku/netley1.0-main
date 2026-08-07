<x-filament-panels::page>
    <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>
</x-filament-panels::page>
