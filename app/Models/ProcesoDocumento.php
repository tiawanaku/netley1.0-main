<?php

namespace App\Models;

use App\Enums\CategoriaDocumento;
use App\Enums\OrigenDocumento;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoDocumento extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (ProcesoDocumento $documento): void {
            if ($documento->origen !== OrigenDocumento::Cliente) {
                return;
            }

            $proceso = $documento->proceso;

            Notification::make()
                ->title('El cliente subió un documento')
                ->body("{$proceso?->tipo_proceso}: {$documento->nombre}")
                ->icon('heroicon-o-document-arrow-up')
                ->sendToDatabase(User::all());
        });
    }

    protected $fillable = [
        'proceso_id',
        'categoria',
        'nombre',
        'archivo',
        'origen',
        'personal_id',
        'cliente_id',
        'solicitud_id',
    ];

    protected function casts(): array
    {
        return [
            'categoria' => CategoriaDocumento::class,
            'origen' => OrigenDocumento::class,
        ];
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function solicitud()
    {
        return $this->belongsTo(DocumentoSolicitud::class, 'solicitud_id');
    }
}
