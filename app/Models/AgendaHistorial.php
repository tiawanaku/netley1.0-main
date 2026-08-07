<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaHistorial extends Model
{
    use HasFactory;

    protected $table = 'agenda_historiales';

    protected $fillable = [
        'agenda_id',
        'usuario_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
        ];
    }

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
