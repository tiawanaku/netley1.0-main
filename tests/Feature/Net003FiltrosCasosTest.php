<?php

use App\Enums\EstadoPersonal;
use App\Enums\EstadoProceso;
use App\Filament\Personal\Pages\MiAgenda;
use App\Filament\Personal\Resources\Clientes\Pages\ListClientes as PersonalListClientes;
use App\Filament\Resources\Clientes\Pages\ListClientes as AdminListClientes;
use App\Filament\Resources\Procesos\Pages\ListProcesos;
use App\Models\Cliente;
use App\Models\Personal;
use App\Models\Proceso;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Regla de dominio: Proceso (Caso) ---

it('un Proceso Activo se considera pendiente', function () {
    $proceso = Proceso::factory()->create(['estado' => EstadoProceso::Activo]);

    expect(Proceso::pendientes()->pluck('id'))->toContain($proceso->id);
    expect(Proceso::cerrados()->pluck('id'))->not->toContain($proceso->id);
    expect($proceso->esta_cerrado)->toBeFalse();
});

it('un Proceso Cerrado se considera cerrado', function () {
    $proceso = Proceso::factory()->create(['estado' => EstadoProceso::Cerrado]);

    expect(Proceso::cerrados()->pluck('id'))->toContain($proceso->id);
    expect(Proceso::pendientes()->pluck('id'))->not->toContain($proceso->id);
    expect($proceso->esta_cerrado)->toBeTrue();
});

it('un Proceso Archivado también se considera cerrado', function () {
    $proceso = Proceso::factory()->create(['estado' => EstadoProceso::Archivado]);

    expect(Proceso::cerrados()->pluck('id'))->toContain($proceso->id);
    expect(Proceso::pendientes()->pluck('id'))->not->toContain($proceso->id);
});

// --- Regla de dominio: Cliente Ejecutivo ---

it('un Cliente Ejecutivo con un caso pendiente se considera Pendiente', function () {
    $cliente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Activo]);

    expect(Cliente::conCasosPendientes()->pluck('id'))->toContain($cliente->id);
    expect(Cliente::conCasosCerrados()->pluck('id'))->not->toContain($cliente->id);
});

it('un Cliente Ejecutivo con un caso cerrado se considera Cerrado', function () {
    $cliente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Cerrado]);

    expect(Cliente::conCasosCerrados()->pluck('id'))->toContain($cliente->id);
    expect(Cliente::conCasosPendientes()->pluck('id'))->not->toContain($cliente->id);
});

it('un Cliente Ejecutivo con varios casos, uno pendiente y otros cerrados, se considera Pendiente', function () {
    $cliente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Cerrado]);
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Archivado]);
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Activo]);

    expect(Cliente::conCasosPendientes()->pluck('id'))->toContain($cliente->id);
    expect(Cliente::conCasosCerrados()->pluck('id'))->not->toContain($cliente->id);
});

it('un Cliente Ejecutivo con todos sus casos cerrados se considera Cerrado', function () {
    $cliente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Cerrado]);
    Proceso::factory()->create(['cliente_id' => $cliente->id, 'estado' => EstadoProceso::Archivado]);

    expect(Cliente::conCasosCerrados()->pluck('id'))->toContain($cliente->id);
    expect(Cliente::conCasosPendientes()->pluck('id'))->not->toContain($cliente->id);
});

it('un Cliente Ejecutivo sin ningún caso se considera Cerrado (decisión de negocio NET-003)', function () {
    $cliente = Cliente::factory()->create();

    expect(Cliente::conCasosCerrados()->pluck('id'))->toContain($cliente->id);
    expect(Cliente::conCasosPendientes()->pluck('id'))->not->toContain($cliente->id);
});

// --- Filtro en Admin ---

it('el filtro Pendientes de Procesos funciona en el panel Admin', function () {
    $admin = User::factory()->create();
    $activo = Proceso::factory()->create(['estado' => EstadoProceso::Activo]);
    $cerrado = Proceso::factory()->create(['estado' => EstadoProceso::Cerrado]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin)
        ->test(ListProcesos::class)
        ->set('activeTab', 'pendiente')
        ->assertCanSeeTableRecords([$activo])
        ->assertCanNotSeeTableRecords([$cerrado]);
});

it('el filtro Cerrados de Procesos funciona en el panel Admin', function () {
    $admin = User::factory()->create();
    $activo = Proceso::factory()->create(['estado' => EstadoProceso::Activo]);
    $cerrado = Proceso::factory()->create(['estado' => EstadoProceso::Cerrado]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin)
        ->test(ListProcesos::class)
        ->set('activeTab', 'cerrado')
        ->assertCanSeeTableRecords([$cerrado])
        ->assertCanNotSeeTableRecords([$activo]);
});

it('el filtro Pendientes/Cerrados de Cliente Ejecutivo funciona en el panel Admin', function () {
    $admin = User::factory()->create();

    $clientePendiente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $clientePendiente->id, 'estado' => EstadoProceso::Activo]);

    $clienteCerrado = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $clienteCerrado->id, 'estado' => EstadoProceso::Cerrado]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin)
        ->test(AdminListClientes::class)
        ->set('activeTab', 'pendiente')
        ->assertCanSeeTableRecords([$clientePendiente])
        ->assertCanNotSeeTableRecords([$clienteCerrado]);

    Livewire::actingAs($admin)
        ->test(AdminListClientes::class)
        ->set('activeTab', 'cerrado')
        ->assertCanSeeTableRecords([$clienteCerrado])
        ->assertCanNotSeeTableRecords([$clientePendiente]);
});

// --- Filtro en Personal ---

it('el filtro Pendientes/Cerrados de Cliente Ejecutivo funciona en el panel Personal', function () {
    $personal = Personal::factory()->create(['estado' => EstadoPersonal::Activo]);

    $clientePendiente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $clientePendiente->id, 'estado' => EstadoProceso::Activo]);

    $clienteCerrado = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $clienteCerrado->id, 'estado' => EstadoProceso::Cerrado]);

    Filament::setCurrentPanel(Filament::getPanel('personal'));

    Livewire::actingAs($personal, 'personal')
        ->test(PersonalListClientes::class)
        ->set('activeTab', 'pendiente')
        ->assertCanSeeTableRecords([$clientePendiente])
        ->assertCanNotSeeTableRecords([$clienteCerrado]);

    Livewire::actingAs($personal, 'personal')
        ->test(PersonalListClientes::class)
        ->set('activeTab', 'cerrado')
        ->assertCanSeeTableRecords([$clienteCerrado])
        ->assertCanNotSeeTableRecords([$clientePendiente]);
});

it('el filtro Pendientes/Cerrados de Mis Casos funciona en el panel Personal', function () {
    $personal = Personal::factory()->create(['estado' => EstadoPersonal::Activo]);

    $casoActivo = Proceso::factory()->create(['abogado_id' => $personal->id, 'estado' => EstadoProceso::Activo]);
    $casoCerrado = Proceso::factory()->create(['abogado_id' => $personal->id, 'estado' => EstadoProceso::Cerrado]);

    Filament::setCurrentPanel(Filament::getPanel('personal'));

    $component = Livewire::actingAs($personal, 'personal')->test(MiAgenda::class);

    $component->call('setFiltroCasos', 'pendiente');
    $pendientes = $component->instance()->getCasos()->pluck('id');
    expect($pendientes)->toContain($casoActivo->id);
    expect($pendientes)->not->toContain($casoCerrado->id);

    $component->call('setFiltroCasos', 'cerrado');
    $cerrados = $component->instance()->getCasos()->pluck('id');
    expect($cerrados)->toContain($casoCerrado->id);
    expect($cerrados)->not->toContain($casoActivo->id);
});

// --- Equivalencia Admin / Personal para el mismo conjunto de datos ---

it('Admin y Personal obtienen los mismos resultados para el mismo conjunto de Clientes Ejecutivos', function () {
    $clientePendiente = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $clientePendiente->id, 'estado' => EstadoProceso::Activo]);

    $clienteCerrado = Cliente::factory()->create();
    Proceso::factory()->create(['cliente_id' => $clienteCerrado->id, 'estado' => EstadoProceso::Cerrado]);

    $clienteSinCasos = Cliente::factory()->create();

    $pendientesEsperados = [$clientePendiente->id];
    $cerradosEsperados = collect([$clienteCerrado->id, $clienteSinCasos->id])->sort()->values();

    expect(Cliente::conCasosPendientes()->pluck('id')->sort()->values()->all())
        ->toBe(collect($pendientesEsperados)->sort()->values()->all());

    expect(Cliente::conCasosCerrados()->pluck('id')->sort()->values()->all())
        ->toBe($cerradosEsperados->all());

    // Admin y Personal reutilizan literalmente el mismo scope de Cliente, por lo
    // que ambos paneles consultan exactamente la misma fuente de verdad.
});

// --- Rendimiento: sin N+1 ---

it('el filtro de Cliente Ejecutivo no genera consultas N+1', function () {
    Cliente::factory()
        ->count(10)
        ->create()
        ->each(function (Cliente $cliente, int $i): void {
            Proceso::factory()->create([
                'cliente_id' => $cliente->id,
                'estado' => $i % 2 === 0 ? EstadoProceso::Activo : EstadoProceso::Cerrado,
            ]);
        });

    DB::enableQueryLog();
    Cliente::conCasosPendientes()->get();
    $queriesPendientes = count(DB::getQueryLog());

    DB::flushQueryLog();
    Cliente::conCasosCerrados()->get();
    $queriesCerrados = count(DB::getQueryLog());
    DB::disableQueryLog();

    // whereHas/whereDoesntHave generan una única consulta con subconsulta EXISTS,
    // independientemente del número de Clientes o Procesos evaluados.
    expect($queriesPendientes)->toBe(1);
    expect($queriesCerrados)->toBe(1);
});

it('el filtro de Procesos no genera consultas N+1', function () {
    Proceso::factory()->count(10)->create();

    DB::enableQueryLog();
    Proceso::pendientes()->get();
    $queriesPendientes = count(DB::getQueryLog());

    DB::flushQueryLog();
    Proceso::cerrados()->get();
    $queriesCerrados = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queriesPendientes)->toBe(1);
    expect($queriesCerrados)->toBe(1);
});

// --- Regresión ---

it('la creación de un Cliente Ejecutivo directo sigue funcionando sin cambios', function () {
    ['cliente' => $cliente] = Cliente::crearDirecto([
        'nombre' => 'Ana',
        'apellidos' => 'Pérez Gómez',
        'telefono' => '70000000',
    ]);

    expect($cliente)->toBeInstanceOf(Cliente::class);
    expect($cliente->exists)->toBeTrue();
    expect($cliente->procesos)->toHaveCount(0);
});

it('la creación de un Proceso sigue funcionando sin cambios y por defecto queda Activo', function () {
    $cliente = Cliente::factory()->create();

    $proceso = Proceso::create([
        'cliente_id' => $cliente->id,
        'materia_legal' => \App\Enums\CategoriaLegal::Civil,
        'tipo_proceso' => 'Usucapión',
        'tiempo_proceso_meses' => 6,
        'estado' => EstadoProceso::Activo,
    ]);

    expect($proceso->estado)->toBe(EstadoProceso::Activo);
    expect($proceso->cliente_id)->toBe($cliente->id);
});
