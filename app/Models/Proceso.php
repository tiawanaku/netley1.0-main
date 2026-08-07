<?php

namespace App\Models;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoProceso;
use App\Enums\TipoAgenda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Proceso extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'materia_legal',
        'tipo_proceso',
        'tiempo_proceso_meses',
        'estado',
        'abogado_id',
    ];

    protected function casts(): array
    {
        return [
            'materia_legal' => CategoriaLegal::class,
            'estado' => EstadoProceso::class,
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function finanza()
    {
        return $this->hasOne(Finanza::class);
    }

    public function abogado()
    {
        return $this->belongsTo(Personal::class, 'abogado_id');
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function documentos()
    {
        return $this->hasMany(ProcesoDocumento::class);
    }

    public function solicitudesDocumento()
    {
        return $this->hasMany(DocumentoSolicitud::class);
    }

    public function getResumenAttribute(): string
    {
        return "{$this->cliente->nombre_completo} — {$this->tipo_proceso}";
    }

    /**
     * Línea de tiempo unificada del caso: consulta, conversión a cliente,
     * registro del proceso, agenda (citas/llamadas), documentos y cuotas.
     *
     * @return Collection<int, array{fecha: \Illuminate\Support\Carbon, titulo: string, descripcion: ?string, tipo: string}>
     */
    public function timeline(): Collection
    {
        $this->loadMissing(['cliente.consulta', 'agendas', 'documentos', 'finanza.planPagos']);

        $eventos = collect();

        if ($consulta = $this->cliente?->consulta) {
            $eventos->push([
                'fecha' => $consulta->created_at,
                'titulo' => 'Consulta registrada',
                'descripcion' => $consulta->descripcion,
                'tipo' => 'consulta',
            ]);
        }

        if ($this->cliente) {
            $eventos->push([
                'fecha' => $this->cliente->created_at,
                'titulo' => 'Cliente Ejecutivo creado',
                'descripcion' => "Usuario: {$this->cliente->usuario}",
                'tipo' => 'cliente',
            ]);
        }

        $eventos->push([
            'fecha' => $this->created_at,
            'titulo' => 'Proceso registrado',
            'descripcion' => "{$this->materia_legal->getLabel()} — {$this->tipo_proceso}",
            'tipo' => 'proceso',
        ]);

        foreach ($this->agendas as $agenda) {
            $eventos->push([
                'fecha' => $agenda->fecha_inicio,
                'titulo' => $agenda->tipo === TipoAgenda::Cita ? 'Cita agendada' : ($agenda->tipo === TipoAgenda::Llamada ? 'Llamada registrada' : 'Reunión agendada'),
                'descripcion' => trim(($agenda->asunto ?? '') . ' — ' . $agenda->estado->getLabel(), ' —'),
                'tipo' => 'agenda',
            ]);
        }

        foreach ($this->documentos as $documento) {
            $eventos->push([
                'fecha' => $documento->created_at,
                'titulo' => "Documento subido: {$documento->nombre}",
                'descripcion' => $documento->categoria->getLabel(),
                'tipo' => 'documento',
            ]);
        }

        foreach ($this->finanza?->planPagos ?? [] as $cuota) {
            $eventos->push([
                'fecha' => $cuota->created_at,
                'titulo' => "Cuota generada: Bs. {$cuota->monto}",
                'descripcion' => "Vence {$cuota->fecha->format('d/m/Y')} — {$cuota->estado->getLabel()}",
                'tipo' => 'pago',
            ]);

            if ($cuota->pagado_en) {
                $eventos->push([
                    'fecha' => $cuota->pagado_en,
                    'titulo' => "Pago registrado: Bs. {$cuota->monto}",
                    'descripcion' => $cuota->estado->getLabel(),
                    'tipo' => 'pago',
                ]);
            }
        }

        return $eventos
            ->filter(fn (array $evento): bool => $evento['fecha'] !== null)
            ->sortBy('fecha')
            ->values();
    }
}
