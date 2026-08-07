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
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
