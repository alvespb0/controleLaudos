<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dados_Cobranca extends Model
{
    protected $table = 'dados_cobranca';

    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'cep',
        'bairro',
        'rua',
        'numero',
        'complemento',
        'cidade',
        'uf',
        'email_cobranca',
        'telefone_cobranca'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
}
