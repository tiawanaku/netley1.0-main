<?php

namespace Database\Seeders;

use App\Models\Personal;
use Illuminate\Database\Seeder;

class PersonalSeeder extends Seeder
{
    public function run(): void
    {
        Personal::factory()->count(10)->create();
    }
}
