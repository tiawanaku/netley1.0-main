<?php

namespace App\Models;

use App\Enums\EstadoConsulta;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use RuntimeException;

class Cliente extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'consulta_id',
        'nombre',
        'apellidos',
        'ci',
        'telefono',
        'whatsapp',
        'usuario',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }

    public function procesos()
    {
        return $this->hasMany(Proceso::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidos}");
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'cliente';
    }

    public function getFilamentName(): string
    {
        return $this->nombre_completo;
    }

    /**
     * @return array{cliente: self, password: string}
     */
    public static function convertirDesdeConsulta(Consulta $consulta): array
    {
        if ($consulta->cliente()->exists()) {
            throw new RuntimeException('Esta consulta ya fue convertida en cliente.');
        }

        $resultado = static::crear([
            'consulta_id' => $consulta->id,
            'nombre' => $consulta->nombre,
            'apellidos' => trim("{$consulta->apellido_paterno} {$consulta->apellido_materno}"),
            'ci' => $consulta->ci,
            'telefono' => $consulta->telefono,
            'whatsapp' => $consulta->whatsapp,
        ]);

        $consulta->update(['estado' => EstadoConsulta::ClienteEjecutivo]);

        return $resultado;
    }

    /**
     * Crea un Cliente Ejecutivo sin una Consulta de origen (alta directa
     * hecha por administración o personal).
     *
     * @return array{cliente: self, password: string}
     */
    public static function crearDirecto(array $datos): array
    {
        return static::crear([
            'consulta_id' => null,
            'nombre' => $datos['nombre'],
            'apellidos' => $datos['apellidos'],
            'ci' => $datos['ci'] ?? null,
            'telefono' => $datos['telefono'],
            'whatsapp' => $datos['whatsapp'] ?? null,
        ]);
    }

    /**
     * @return array{cliente: self, password: string}
     */
    protected static function crear(array $datos): array
    {
        $primerApellido = Str::of($datos['apellidos'])->trim()->explode(' ')->first() ?: $datos['apellidos'];
        $usuario = static::generarUsuario($datos['nombre'], $primerApellido);
        $password = Str::password(10);

        $cliente = static::create([
            ...$datos,
            'usuario' => $usuario,
            'password' => $password,
        ]);

        return ['cliente' => $cliente, 'password' => $password];
    }

    protected static function generarUsuario(string $nombre, string $apellido): string
    {
        $base = Str::of("{$nombre} {$apellido}")
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]/', '')
            ->squish()
            ->replace(' ', '.')
            ->toString();

        $usuario = $base;
        $suffix = 1;

        while (static::where('usuario', $usuario)->exists()) {
            $usuario = "{$base}{$suffix}";
            $suffix++;
        }

        return $usuario;
    }
}
