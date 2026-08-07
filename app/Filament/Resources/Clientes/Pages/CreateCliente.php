<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Concerns\CreaClienteEjecutivoDirecto;
use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    use CreaClienteEjecutivoDirecto;

    protected static string $resource = ClienteResource::class;
}
