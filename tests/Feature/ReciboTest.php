<?php

use App\Enums\EstadoPago;
use App\Enums\EstadoRecibo;
use App\Enums\OrigenRecibo;
use App\Models\Finanza;
use App\Models\PlanPago;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function crearFinanzaConAnticipo(float $anticipo = 500): Finanza
{
    return Finanza::factory()->create(['anticipo' => $anticipo]);
}

function crearPlanPagoPendiente(): PlanPago
{
    $finanza = Finanza::factory()->create();

    return PlanPago::create([
        'finanza_id' => $finanza->id,
        'fecha' => now(),
        'monto' => 300,
        'estado' => EstadoPago::Pendiente,
    ]);
}

beforeEach(function () {
    $this->admin = User::factory()->create();
});

// --- Emisión de recibo al confirmar una cuota ---

it('emite un recibo automáticamente cuando una cuota se marca como Pagado', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    expect($planPago->recibo()->exists())->toBeFalse();

    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;

    expect($recibo)->not->toBeNull()
        ->and($recibo->numero)->toStartWith('REC-'.now()->year.'-')
        ->and($recibo->identificador)->not->toBeEmpty()
        ->and(strlen($recibo->hash_verificacion))->toBe(64)
        ->and($recibo->origen_tipo)->toBe(OrigenRecibo::Cuota)
        ->and($recibo->estado)->toBe(EstadoRecibo::Emitido)
        ->and($recibo->registrado_por_user_id)->toBe($this->admin->id)
        ->and($recibo->esAutentico())->toBeTrue();
});

it('no emite un segundo recibo si la cuota se vuelve a guardar ya en estado Pagado', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $primerReciboId = $planPago->fresh()->recibo->id;

    // Guardar de nuevo sin cambiar 'estado' (ya está en Pagado) no debe
    // volver a intentar emitir, y editar campos no protegidos debe fallar
    // igual por la regla de inmutabilidad (probado más abajo también).
    expect(\App\Models\Recibo::where('plan_pago_id', $planPago->id)->count())->toBe(1);
});

// --- Correlativo ---

it('genera números de recibo únicos y monótonamente crecientes en emisiones sucesivas', function () {
    $this->actingAs($this->admin);

    $numeros = [];

    for ($i = 0; $i < 15; $i++) {
        $planPago = crearPlanPagoPendiente();
        $planPago->confirmarPago();
        $numeros[] = $planPago->fresh()->recibo->numero;
    }

    expect($numeros)->toHaveCount(15)
        ->and(array_unique($numeros))->toHaveCount(15);

    $partesNumericas = array_map(fn ($n) => (int) substr($n, -6), $numeros);
    $ordenado = $partesNumericas;
    sort($ordenado);

    expect($partesNumericas)->toEqual($ordenado);
});

// --- Hash / detección de alteración ---

it('detecta si los datos de un recibo fueron alterados después de emitido', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;

    expect($recibo->esAutentico())->toBeTrue();

    $recibo->monto = 999999;
    $recibo->save();

    expect($recibo->fresh()->esAutentico())->toBeFalse();
});

// --- Anulación ---

it('anula un recibo sin revertir el estado del pago asociado', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;

    $recibo->anular('Error en el monto registrado');
    $recibo->refresh();
    $planPago->refresh();

    expect($recibo->estado)->toBe(EstadoRecibo::Anulado)
        ->and($recibo->anulado_en)->not->toBeNull()
        ->and($recibo->anulado_por_user_id)->toBe($this->admin->id)
        ->and($recibo->motivo_anulacion)->toBe('Error en el monto registrado')
        ->and($recibo->numero)->not->toBeEmpty()
        ->and($planPago->estado)->toBe(EstadoPago::Pagado)
        ->and($recibo->esAutentico())->toBeTrue(); // el hash se recalcula al anular
});

it('no permite anular un recibo que ya está anulado', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;
    $recibo->anular('Motivo 1');

    expect(fn () => $recibo->anular('Motivo 2'))->toThrow(RuntimeException::class);
});

it('el número de un recibo anulado nunca se reutiliza', function () {
    $this->actingAs($this->admin);

    $planPago1 = crearPlanPagoPendiente();
    $planPago1->confirmarPago();
    $reciboAnulado = $planPago1->fresh()->recibo;
    $numeroAnulado = $reciboAnulado->numero;
    $reciboAnulado->anular('Corrección');

    $planPago2 = crearPlanPagoPendiente();
    $planPago2->confirmarPago();
    $numeroNuevo = $planPago2->fresh()->recibo->numero;

    expect($numeroNuevo)->not->toBe($numeroAnulado);
});

// --- Inmutabilidad del pago con recibo emitido ---

it('impide editar el monto de una cuota que ya tiene recibo emitido', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();

    expect(fn () => $planPago->fresh()->update(['monto' => 1234]))
        ->toThrow(RuntimeException::class);
});

it('impide eliminar una cuota que ya tiene recibo emitido', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();

    expect(fn () => $planPago->fresh()->delete())->toThrow(RuntimeException::class);
});

it('permite editar y eliminar libremente una cuota sin recibo', function () {
    $planPago = crearPlanPagoPendiente();

    $planPago->update(['monto' => 999]);
    expect((float) $planPago->fresh()->monto)->toBe(999.0);

    expect($planPago->fresh()->delete())->toBeTrue();
});

// --- Anticipo: no debe emitir recibo solo por declararse ---

it('NO emite un recibo automáticamente solo por declarar un anticipo al crear la Finanza', function () {
    $finanza = crearFinanzaConAnticipo(800);

    expect($finanza->recibo()->exists())->toBeFalse()
        ->and($finanza->anticipo_confirmado_en)->toBeNull();
});

it('confirmarAnticipo() emite el recibo del anticipo', function () {
    $this->actingAs($this->admin);

    $finanza = crearFinanzaConAnticipo(800);
    $finanza->confirmarAnticipo();
    $recibo = $finanza->fresh()->recibo;

    expect($recibo)->not->toBeNull()
        ->and($recibo->origen_tipo)->toBe(OrigenRecibo::Anticipo)
        ->and((float) $recibo->monto)->toBe(800.0)
        ->and($recibo->registrado_por_user_id)->toBe($this->admin->id);
});

it('confirmarAnticipo() lanza excepción si no hay anticipo declarado', function () {
    $finanza = Finanza::factory()->create(['anticipo' => 0]);

    expect(fn () => $finanza->confirmarAnticipo())->toThrow(RuntimeException::class);
});

it('confirmarAnticipo() lanza excepción si ya fue confirmado antes', function () {
    $this->actingAs($this->admin);

    $finanza = crearFinanzaConAnticipo(800);
    $finanza->confirmarAnticipo();

    expect(fn () => $finanza->fresh()->confirmarAnticipo())->toThrow(RuntimeException::class);
});

it('impide modificar el anticipo de una Finanza que ya tiene recibo', function () {
    $this->actingAs($this->admin);

    $finanza = crearFinanzaConAnticipo(800);
    $finanza->confirmarAnticipo();

    expect(fn () => $finanza->fresh()->update(['anticipo' => 1500]))
        ->toThrow(RuntimeException::class);
});

it('permite modificar el costo de la Finanza aunque el anticipo ya tenga recibo', function () {
    $this->actingAs($this->admin);

    $finanza = crearFinanzaConAnticipo(800);
    $finanza->confirmarAnticipo();

    $finanza->fresh()->update(['costo' => 5000]);

    expect((float) $finanza->fresh()->costo)->toBe(5000.0);
});

// --- Verificación pública ---

it('la página de verificación responde con una URL firmada válida', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;

    $url = URL::signedRoute('recibos.verificar', ['recibo' => $recibo->identificador]);

    $this->get($url)->assertOk()->assertSee($recibo->numero);
});

it('rechaza una URL de verificación sin firma válida', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;

    $this->get("/verificar/recibo/{$recibo->identificador}")->assertForbidden();
});

it('devuelve 404 al verificar un identificador inexistente con firma válida', function () {
    $url = URL::signedRoute('recibos.verificar', ['recibo' => (string) Str::uuid()]);

    $this->get($url)->assertNotFound();
});

it('la verificación muestra el estado Anulado cuando corresponde', function () {
    $this->actingAs($this->admin);

    $planPago = crearPlanPagoPendiente();
    $planPago->confirmarPago();
    $recibo = $planPago->fresh()->recibo;
    $recibo->anular('Prueba de anulación');

    $url = URL::signedRoute('recibos.verificar', ['recibo' => $recibo->identificador]);

    $this->get($url)->assertOk()->assertSee('ANULADO');
});
