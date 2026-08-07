<?php

namespace Database\Seeders;

use App\Models\Consulta;
use Illuminate\Database\Seeder;

class ConsultaSeeder extends Seeder
{
    public function run(): void
    {
        Consulta::factory()->count(15)->create();
    }
}
