<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variaveis_Precificacao extends Model
{
    protected $table = 'variaveis_precificacao';

    use HasFactory;

    protected $fillable = [
        'nome',
        'campo_alvo',
        'tipo',
        'valor',
        'ativo'
    ];

    public function faixas(){
        return $this->hasMany(Faixa_Precificacao::class, 'variavel_id');
    }
}
