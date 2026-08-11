<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_id',
        'respuesta',
        'personal_id',
        'user_id',
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRespondidoPorAttribute(): ?string
    {
        return $this->personal?->nombre_completo ?? $this->user?->name;
    }
}
