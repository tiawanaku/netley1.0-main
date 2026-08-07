<?php

namespace App\Models;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoConsulta;
use App\Enums\FormaIngreso;
use App\Enums\OrigenConsulta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
