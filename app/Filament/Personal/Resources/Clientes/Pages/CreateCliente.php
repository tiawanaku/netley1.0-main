<?php

namespace App\Filament\Personal\Resources\Clientes\Pages;

use App\Filament\Concerns\CreaClienteEjecutivoDirecto;
use App\Filament\Personal\Resources\Clientes\ClienteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    use CreaClienteEjecutivoDirecto;

    protected static string $resource = ClienteResource::class;
}
