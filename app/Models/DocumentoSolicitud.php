<?php

namespace App\Models;

use App\Enums\EstadoSolicitudDocumento;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoSolicitud extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (DocumentoSolicitud $solicitud): void {
            if ($cliente = $solicitud->proceso?->cliente) {
                Notification::make()
                    ->title('Tu abogado solicitó un documento')
                    ->body($solicitud->descripcion)
                    ->icon('heroicon-o-document-arrow-up')
                    ->sendToDatabase($cliente);
            }
        });
    }

    protected $table = 'documento_solicitudes';

    protected $fillable = [
        'proceso_id',
        'personal_id',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoSolicitudDocumento::class,
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

    public function documentos()
    {
        return $this->hasMany(ProcesoDocumento::class, 'solicitud_id');
    }

    public function marcarCumplida(): void
    {
        $this->update(['estado' => EstadoSolicitudDocumento::Cumplida]);
    }
}
