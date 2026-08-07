<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Consulta;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Consulta::doesntHave('cliente')
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->each(fn (Consulta $consulta) => Cliente::convertirDesdeConsulta($consulta));
    }
}
