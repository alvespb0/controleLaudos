<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laudo extends Model
{
    protected $table = 'laudos';

    use HasFactory;

    protected $fillable = [
        'nome',
        'data_previsao',
        'data_conclusao',
        'data_fim_contrato',
        'tecnico_id',
        'status_id',
        'cliente_id',
        'comercial_id',
    ];
}
