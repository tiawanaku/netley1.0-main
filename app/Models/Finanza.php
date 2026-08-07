<?php

namespace App\Models;

use App\Enums\EstadoPago;
use App\Enums\TipoPago;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use RuntimeException;

class Finanza extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'costo',
        'tipo_pago',
        'anticipo',
        'anticipo_registrado_en',
    ];

    protected function casts(): array
    {
        return [
            'costo' => 'decimal:2',
            'tipo_pago' => TipoPago::class,
            'anticipo' => 'decimal:2',
            'anticipo_registrado_en' => 'datetime',
        ];
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function planPagos()
    {
        return $this->hasMany(PlanPago::class);
    }

    /**
     * Número máximo de cuotas permitido según el tiempo del proceso asociado.
     */
    public function maxCuotasPermitidas(): int
    {
        $meses = $this->proceso->tiempo_proceso_meses;

        return $this->tipo_pago === TipoPago::Semanal
            ? (int) round($meses * 52 / 12)
            : (int) $meses;
    }

    /**
     * @return Collection<int, PlanPago>
     */
    public function generarPlanPagos(int $cuotas, Carbon|string $fechaInicio): Collection
    {
        if ($cuotas < 1) {
            throw new RuntimeException('El número de cuotas debe ser al menos 1.');
        }

        $maximo = $this->maxCuotasPermitidas();

        if ($cuotas > $maximo) {
            throw new RuntimeException("El número de cuotas ({$cuotas}) excede el máximo permitido ({$maximo}) según el tiempo del proceso.");
        }

        if ($this->planPagos()->where('estado', EstadoPago::Pagado)->exists()) {
            throw new RuntimeException('No se puede regenerar el plan de pagos: ya existen cuotas pagadas.');
        }

        $this->planPagos()->delete();

        $fechaInicio = $fechaInicio instanceof Carbon ? $fechaInicio : Carbon::parse($fechaInicio);
        $montoAFinanciar = $this->costo - ($this->anticipo ?? 0);
        $montoBase = round($montoAFinanciar / $cuotas, 2);
        $montoAcumulado = 0;

        $planPagos = collect();

        for ($i = 0; $i < $cuotas; $i++) {
            $esUltima = $i === $cuotas - 1;
            $monto = $esUltima ? round($montoAFinanciar - $montoAcumulado, 2) : $montoBase;
            $montoAcumulado += $monto;

            $fecha = $this->tipo_pago === TipoPago::Semanal
                ? $fechaInicio->copy()->addWeeks($i)
                : $fechaInicio->copy()->addMonths($i);

            $planPagos->push($this->planPagos()->create([
                'fecha' => $fecha,
                'monto' => $monto,
                'estado' => EstadoPago::Pendiente,
            ]));
        }

        return $planPagos;
    }
}
