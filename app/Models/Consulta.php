<?php

namespace App\Models;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoConsulta;
use App\Enums\FormaIngreso;
use App\Enums\OrigenConsulta;
use App\Enums\TipoAgenda;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Consulta extends Model
{
    use HasFactory;

    public const CIUDADES = [
        'la_paz' => 'La Paz',
        'santa_cruz' => 'Santa Cruz',
        'cochabamba' => 'Cochabamba',
        'sucre' => 'Sucre',
        'oruro' => 'Oruro',
        'potosi' => 'Potosí',
        'tarija' => 'Tarija',
        'trinidad' => 'Trinidad',
        'cobija' => 'Cobija',
    ];

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'telefono',
        'whatsapp',
        'ciudad',
        'email',
        'tipo_proceso',
        'descripcion',
        'origen',
        'forma_ingreso',
        'colegio_otros',
        'estado',
        'pago_inicial_monto',
        'pago_inicial_registrado_en',
        'atendido_por',
    ];

    protected function casts(): array
    {
        return [
            'tipo_proceso' => CategoriaLegal::class,
            'origen' => OrigenConsulta::class,
            'forma_ingreso' => FormaIngreso::class,
            'estado' => EstadoConsulta::class,
            'pago_inicial_monto' => 'decimal:2',
            'pago_inicial_registrado_en' => 'datetime',
        ];
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function atendidoPor()
    {
        return $this->belongsTo(Personal::class, 'atendido_por');
    }

    /**
     * Datos listos para el informe de la consulta: una fila por cada respuesta
     * registrada en sus citas, con quién tomó la cita, quién respondió y el
     * tiempo transcurrido desde el registro de la consulta hasta la respuesta
     * (métrica prevista para uso futuro).
     */
    public function respuestas(): Collection
    {
        return $this->agendas()
            ->where('tipo', TipoAgenda::Cita)
            ->with(['responsable', 'answers.personal', 'answers.user'])
            ->get()
            ->flatMap(fn (Agenda $agenda) => $agenda->answers->map(fn (Answer $answer) => [
                'tomada_por' => $agenda->responsable?->nombre_completo ?? 'Sin asignar',
                'fecha_cita' => $agenda->fecha_inicio?->format('d/m/Y H:i'),
                'respondido_por' => $answer->respondido_por ?? 'Desconocido',
                'fecha_respuesta' => $answer->created_at?->format('d/m/Y H:i'),
                'fecha_respuesta_orden' => $answer->created_at,
                'tiempo_respuesta' => $answer->created_at
                    ? $this->created_at->diffForHumans($answer->created_at, syntax: CarbonInterface::DIFF_ABSOLUTE)
                    : null,
                'contenido' => $answer->respuesta,
            ]))
            ->sortByDesc('fecha_respuesta_orden')
            ->map(fn (array $fila): array => Arr::except($fila, 'fecha_respuesta_orden'))
            ->values();
    }
}
