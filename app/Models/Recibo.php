<?php

namespace App\Models;

use App\Enums\EstadoRecibo;
use App\Enums\OrigenRecibo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class Recibo extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'identificador',
        'hash_verificacion',
        'origen_tipo',
        'plan_pago_id',
        'finanza_id',
        'cliente_id',
        'proceso_id',
        'concepto',
        'monto',
        'moneda',
        'fecha_pago',
        'estado',
        'registrado_por_personal_id',
        'registrado_por_user_id',
        'anulado_en',
        'anulado_por_personal_id',
        'anulado_por_user_id',
        'motivo_anulacion',
    ];

    protected function casts(): array
    {
        return [
            'origen_tipo' => OrigenRecibo::class,
            'estado' => EstadoRecibo::class,
            'monto' => 'decimal:2',
            'fecha_pago' => 'date',
            'anulado_en' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Recibo $recibo): void {
            if (blank($recibo->identificador)) {
                $recibo->identificador = (string) Str::uuid();
            }
        });
    }

    public function planPago()
    {
        return $this->belongsTo(PlanPago::class);
    }

    public function finanza()
    {
        return $this->belongsTo(Finanza::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function registradoPorPersonal()
    {
        return $this->belongsTo(Personal::class, 'registrado_por_personal_id');
    }

    public function registradoPorUser()
    {
        return $this->belongsTo(User::class, 'registrado_por_user_id');
    }

    public function anuladoPorPersonal()
    {
        return $this->belongsTo(Personal::class, 'anulado_por_personal_id');
    }

    public function anuladoPorUser()
    {
        return $this->belongsTo(User::class, 'anulado_por_user_id');
    }

    public function getRegistradoPorAttribute(): ?string
    {
        return $this->registradoPorPersonal?->nombre_completo ?? $this->registradoPorUser?->name;
    }

    public function getAnuladoPorAttribute(): ?string
    {
        return $this->anuladoPorPersonal?->nombre_completo ?? $this->anuladoPorUser?->name;
    }

    /**
     * Emite el recibo de una cuota del plan de pagos que acaba de marcarse
     * como Pagada. No debe llamarse si la cuota ya tiene recibo (la relación
     * es única a nivel de base de datos, así que un segundo intento fallaría
     * de todas formas por la restricción unique en plan_pago_id).
     */
    public static function emitirParaCuota(PlanPago $planPago): self
    {
        $finanza = $planPago->finanza;
        $proceso = $finanza->proceso;
        $cliente = $proceso->cliente;

        return static::emitir([
            'origen_tipo' => OrigenRecibo::Cuota,
            'plan_pago_id' => $planPago->id,
            'finanza_id' => null,
            'cliente_id' => $cliente->id,
            'proceso_id' => $proceso->id,
            'concepto' => "Cuota — {$proceso->tipo_proceso}",
            'monto' => $planPago->monto,
            'fecha_pago' => $planPago->pagado_en?->toDateString() ?? now()->toDateString(),
        ]);
    }

    /**
     * Emite el recibo del anticipo de una Finanza, una vez confirmado como
     * efectivamente recibido (ver Finanza::confirmarAnticipo()). Nunca se
     * debe llamar solo porque exista un valor en `anticipo`.
     */
    public static function emitirParaAnticipo(Finanza $finanza): self
    {
        $proceso = $finanza->proceso;
        $cliente = $proceso->cliente;

        return static::emitir([
            'origen_tipo' => OrigenRecibo::Anticipo,
            'plan_pago_id' => null,
            'finanza_id' => $finanza->id,
            'cliente_id' => $cliente->id,
            'proceso_id' => $proceso->id,
            'concepto' => "Anticipo — {$proceso->tipo_proceso}",
            'monto' => $finanza->anticipo,
            'fecha_pago' => $finanza->anticipo_confirmado_en?->toDateString() ?? now()->toDateString(),
        ]);
    }

    /**
     * @param  array{origen_tipo: OrigenRecibo, plan_pago_id: ?int, finanza_id: ?int, cliente_id: int, proceso_id: int, concepto: string, monto: mixed, fecha_pago: string}  $datos
     */
    protected static function emitir(array $datos): self
    {
        // El correlativo se asigna en su propia transacción, corta y ya
        // confirmada, ANTES de crear el recibo. Si el resto de esta creación
        // falla después, el número ya quedó consumido y jamás se reutiliza
        // (no se garantiza ausencia de huecos, solo unicidad y no reuso).
        $numero = static::siguienteNumero();

        $actor = static::actorActual();

        $hash = static::calcularHash(
            numero: $numero,
            monto: (string) $datos['monto'],
            fechaPago: $datos['fecha_pago'],
            clienteId: $datos['cliente_id'],
            concepto: $datos['concepto'],
            estado: EstadoRecibo::Emitido->value,
        );

        return static::create([
            ...$datos,
            'numero' => $numero,
            'hash_verificacion' => $hash,
            'moneda' => 'BOB',
            'estado' => EstadoRecibo::Emitido,
            ...$actor,
        ]);
    }

    protected static function siguienteNumero(): string
    {
        return DB::transaction(function (): string {
            $contador = DB::table('recibo_correlativos')->lockForUpdate()->first();

            if (! $contador) {
                throw new RuntimeException('No existe la fila de contador en recibo_correlativos.');
            }

            $siguiente = $contador->ultimo_numero + 1;

            DB::table('recibo_correlativos')
                ->where('id', $contador->id)
                ->update(['ultimo_numero' => $siguiente, 'updated_at' => now()]);

            return sprintf('REC-%s-%06d', now()->year, $siguiente);
        });
    }

    protected static function actorActual(): array
    {
        if (($personal = auth('personal')->user()) instanceof Personal) {
            return ['registrado_por_personal_id' => $personal->id, 'registrado_por_user_id' => null];
        }

        if (($user = auth()->user()) instanceof User) {
            return ['registrado_por_personal_id' => null, 'registrado_por_user_id' => $user->id];
        }

        return ['registrado_por_personal_id' => null, 'registrado_por_user_id' => null];
    }

    public function recalcularHash(): string
    {
        return static::calcularHash(
            numero: $this->numero,
            monto: (string) $this->monto,
            fechaPago: $this->fecha_pago->toDateString(),
            clienteId: $this->cliente_id,
            concepto: $this->concepto,
            estado: $this->estado->value,
        );
    }

    public function esAutentico(): bool
    {
        return hash_equals($this->hash_verificacion, $this->recalcularHash());
    }

    protected static function calcularHash(string $numero, string $monto, string $fechaPago, int $clienteId, string $concepto, string $estado): string
    {
        $datos = implode('|', [$numero, $monto, $fechaPago, $clienteId, $concepto, $estado]);

        return hash_hmac('sha256', $datos, config('app.key'));
    }

    public function anular(string $motivo): void
    {
        if ($this->estado === EstadoRecibo::Anulado) {
            throw new RuntimeException('Este recibo ya está anulado.');
        }

        $actor = [];

        if (($personal = auth('personal')->user()) instanceof Personal) {
            $actor = ['anulado_por_personal_id' => $personal->id, 'anulado_por_user_id' => null];
        } elseif (($user = auth()->user()) instanceof User) {
            $actor = ['anulado_por_personal_id' => null, 'anulado_por_user_id' => $user->id];
        }

        $nuevoHash = static::calcularHash(
            numero: $this->numero,
            monto: (string) $this->monto,
            fechaPago: $this->fecha_pago->toDateString(),
            clienteId: $this->cliente_id,
            concepto: $this->concepto,
            estado: EstadoRecibo::Anulado->value,
        );

        $this->update([
            'estado' => EstadoRecibo::Anulado,
            'anulado_en' => now(),
            'motivo_anulacion' => $motivo,
            'hash_verificacion' => $nuevoHash,
            ...$actor,
        ]);
    }
}
