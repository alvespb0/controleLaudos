<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faixa_Precificacao extends Model
{
    protected $table = 'faixa_precificacao';

    use HasFactory;

    protected $fillable = [
        'variavel_id',
        'valor_min',
        'valor_max',
        'percentual_reajuste',
        'preco_min',
        'preco_max'
    ];

    public function variavel(){
        return $this->belongsTo(Variaveis_Precificacao::class, 'variavel_id');
    }
}
