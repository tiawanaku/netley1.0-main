<?php

namespace App\Models;

use App\Enums\EspecialidadAbogado;
use App\Enums\EstadoCivil;
use App\Enums\EstadoPersonal;
use App\Enums\Genero;
use App\Enums\RolPersonal;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Personal extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory;
    use Notifiable;

    protected $table = 'personal';

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
        'apellidos',
        'ci',
        'genero',
        'fecha_nacimiento',
        'nacionalidad',
        'estado_civil',
        'profesion',
        'telefono',
        'whatsapp',
        'email',
        'direccion',
        'ciudad',
        'numero_contrato',
        'estado',
        'fecha_inicio',
        'rol',
        'especialidad_abogado',
        'foto',
        'documentos',
        'nota',
        'usuario',
        'password',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Contraseña temporal en texto plano, disponible solo en memoria justo
     * después de generarse (creación o restablecimiento), para mostrarla una vez.
     */
    public ?string $plainPassword = null;

    protected function casts(): array
    {
        return [
            'genero' => Genero::class,
            'estado_civil' => EstadoCivil::class,
            'estado' => EstadoPersonal::class,
            'rol' => RolPersonal::class,
            'especialidad_abogado' => EspecialidadAbogado::class,
            'fecha_nacimiento' => 'date',
            'fecha_inicio' => 'date',
            'documentos' => 'array',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Personal $personal): void {
            if (blank($personal->usuario)) {
                $personal->usuario = static::generarUsuarioDesdeTelefono($personal->telefono);
            }

            if (blank($personal->password)) {
                $password = Str::password(10);
                $personal->plainPassword = $password;
                $personal->password = $password;
                $personal->must_change_password = true;
            }
        });
    }

    public function esAbogado(): bool
    {
        return $this->rol === RolPersonal::Abogado;
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidos}");
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'responsable_id');
    }

    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'abogado_id');
    }

    public function recibosRegistrados()
    {
        return $this->hasMany(Recibo::class, 'registrado_por_personal_id');
    }

    public function recibosAnulados()
    {
        return $this->hasMany(Recibo::class, 'anulado_por_personal_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'personal' && $this->estado === EstadoPersonal::Activo;
    }

    public function getFilamentName(): string
    {
        return $this->nombre_completo;
    }

    /**
     * Restablece la contraseña de acceso al Portal de Personal para este
     * miembro del equipo y devuelve la contraseña en texto plano (solo se
     * muestra una vez). Obliga a cambiarla en el siguiente inicio de sesión.
     */
    public function generarAccesoPortal(): string
    {
        if (blank($this->usuario)) {
            $this->usuario = static::generarUsuarioDesdeTelefono($this->telefono);
        }

        $password = Str::password(10);
        $this->password = $password;
        $this->must_change_password = true;
        $this->save();

        return $password;
    }

    /**
     * RN-PER-01: el usuario de acceso es el número de teléfono registrado.
     */
    protected static function generarUsuarioDesdeTelefono(string $telefono): string
    {
        $base = preg_replace('/\D/', '', $telefono) ?: $telefono;

        $usuario = $base;
        $suffix = 1;

        while (static::where('usuario', $usuario)->exists()) {
            $usuario = "{$base}-{$suffix}";
            $suffix++;
        }

        return $usuario;
    }
}
