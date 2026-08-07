<?php

namespace App\Filament\Cliente\Pages;

use App\Models\Cliente;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class MisProcesos extends Page
{
    protected string $view = 'filament.cliente.pages.mis-procesos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Mis Procesos';

    protected static ?string $title = 'Mis Procesos';

    protected static ?int $navigationSort = 1;

    public function getCliente(): Cliente
    {
        /** @var Cliente $cliente */
        $cliente = Filament::auth()->user();

        return $cliente;
    }

    /**
     * @return Collection<int, \App\Models\Proceso>
     */
    public function getProcesos(): Collection
    {
        return $this->getCliente()
            ->procesos()
            ->with(['abogado', 'finanza.planPagos'])
            ->latest()
            ->get();
    }
}
