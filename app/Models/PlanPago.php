<?php

namespace App\Models;

use App\Enums\EstadoPago;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PlanPago extends Model
{
    use HasFactory;

    protected $table = 'plan_pagos';

    protected $fillable = [
        'finanza_id',
        'fecha',
        'monto',
        'estado',
        'qr_path',
        'comprobante',
        'pagado_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto' => 'decimal:2',
            'estado' => EstadoPago::class,
            'pagado_en' => 'datetime',
        ];
    }

    public function finanza()
    {
        return $this->belongsTo(Finanza::class);
    }

    /**
     * Genera (o reutiliza) la imagen QR de referencia de pago para esta cuota
     * y devuelve la ruta en el disco público.
     */
    public function generarQr(): string
    {
        if ($this->qr_path && Storage::disk('public')->exists($this->qr_path)) {
            return $this->qr_path;
        }

        $referencia = "NETLEY-PAGO-{$this->id}-{$this->monto}-{$this->fecha->format('Ymd')}";

        $result = (new Builder(
            writer: new PngWriter(),
            data: $referencia,
            size: 300,
            margin: 10,
        ))->build();

        $path = "pagos/qr/plan-pago-{$this->id}.png";

        Storage::disk('public')->put($path, $result->getString());

        $this->update(['qr_path' => $path]);

        return $path;
    }

    /**
     * Registra el intento de pago del cliente (comprobante subido) y deja
     * la cuota en estado "pendiente de confirmación" hasta que el
     * administrador la verifique manualmente.
     */
    public function registrarPagoCliente(string $comprobantePath): void
    {
        $this->update([
            'comprobante' => $comprobantePath,
            'pagado_en' => now(),
            'estado' => EstadoPago::PendienteConfirmacion,
        ]);

        /** @var Cliente|null $cliente */
        $cliente = $this->finanza->proceso->cliente;

        Notification::make()
            ->title('Nuevo comprobante de pago')
            ->body("{$cliente?->nombre_completo}: Bs. {$this->monto} pendiente de confirmación.")
            ->icon('heroicon-o-banknotes')
            ->sendToDatabase(User::all());
    }

    public function confirmarPago(): void
    {
        $this->update([
            'estado' => EstadoPago::Pagado,
            'pagado_en' => $this->pagado_en ?? now(),
        ]);

        if ($cliente = $this->finanza->proceso->cliente) {
            Notification::make()
                ->title('Tu pago fue confirmado')
                ->body("Bs. {$this->monto} — {$this->fecha->format('d/m/Y')}")
                ->icon('heroicon-o-check-circle')
                ->sendToDatabase($cliente);
        }
    }
}
