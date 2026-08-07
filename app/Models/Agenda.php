<?php

namespace App\Models;

use App\Enums\EstadoAgenda;
use App\Enums\ModalidadAgenda;
use App\Enums\ResultadoLlamada;
use App\Enums\TipoAgenda;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    public const UBICACIONES = [
        'oficina' => 'Oficina',
        'sala' => 'Sala',
        'google_meet' => 'Google Meet',
        'zoom' => 'Zoom',
        'otro' => 'Otro',
    ];

    protected $fillable = [
        'tipo',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'asunto',
        'descripcion',
        'modalidad',
        'ubicacion',
        'duracion_minutos',
        'resultado',
        'proceso_id',
        'consulta_id',
        'cliente_id',
        'responsable_id',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::created(function (Agenda $agenda): void {
            $agenda->historial()->create([
                'usuario_id' => static::usuarioAdminActual(),
                'accion' => 'creado',
                'datos_nuevos' => $agenda->getAttributes(),
            ]);

            if ($agenda->tipo === TipoAgenda::Cita && ($cliente = $agenda->proceso?->cliente)) {
                Notification::make()
                    ->title('Se agendó una cita')
                    ->body($agenda->fecha_inicio->format('d/m/Y H:i'))
                    ->icon('heroicon-o-calendar-days')
                    ->sendToDatabase($cliente);
            }
        });

        static::updated(function (Agenda $agenda): void {
            if (! $agenda->wasChanged()) {
                return;
            }

            $agenda->historial()->create([
                'usuario_id' => static::usuarioAdminActual(),
                'accion' => 'actualizado',
                'datos_anteriores' => $agenda->getOriginal(),
                'datos_nuevos' => $agenda->getChanges(),
            ]);
        });
    }

    protected function casts(): array
    {
        return [
            'tipo' => TipoAgenda::class,
            'estado' => EstadoAgenda::class,
            'modalidad' => ModalidadAgenda::class,
            'resultado' => ResultadoLlamada::class,
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
        ];
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Personal::class, 'responsable_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participantes()
    {
        return $this->belongsToMany(Personal::class, 'agenda_participantes')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function historial()
    {
        return $this->hasMany(AgendaHistorial::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getRelacionadoConAttribute(): ?string
    {
        return match ($this->tipo) {
            TipoAgenda::Cita => $this->proceso?->resumen ?? $this->consulta?->nombre_completo,
            TipoAgenda::Llamada => $this->consulta?->nombre_completo ?? $this->cliente?->nombre_completo,
            TipoAgenda::Reunion => $this->asunto,
            default => null,
        };
    }

    public static function hayConflicto(int $responsableId, Carbon $inicio, Carbon $fin, ?int $excludeId = null): bool
    {
        return static::query()
            ->where('responsable_id', $responsableId)
            ->where('estado', '!=', EstadoAgenda::Cancelada)
            ->when($excludeId, fn ($query) => $query->whereKeyNot($excludeId))
            ->where('fecha_inicio', '<', $fin)
            ->where('fecha_fin', '>', $inicio)
            ->exists();
    }

    /**
     * Regla de horario laboral para agendar desde el Portal de Personal
     * (RN: lunes a viernes, 08:00 a 17:00). El panel Admin no usa esta regla,
     * ahí se puede agendar en cualquier horario.
     */
    public static function reglaHorarioLaboral(): \Closure
    {
        return function (string $attribute, $value, \Closure $fail): void {
            $fecha = Carbon::parse($value);

            if (! $fecha->isWeekday()) {
                $fail('Solo se pueden agendar citas de lunes a viernes.');

                return;
            }

            if ($fecha->format('H:i') < '08:00' || $fecha->format('H:i') > '17:00') {
                $fail('El horario debe estar entre las 08:00 y las 17:00.');
            }
        };
    }

    /**
     * agenda_historiales.usuario_id solo referencia la tabla users (administradores).
     * Las agendas ahora también pueden crearse/editarse desde el Portal de Personal,
     * cuyo actor autenticado es un Personal, no un User, así que no debe registrarse ahí.
     */
    protected static function usuarioAdminActual(): ?int
    {
        $usuario = auth()->user();

        return $usuario instanceof User ? $usuario->id : null;
    }
}
