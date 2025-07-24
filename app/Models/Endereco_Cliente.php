<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco_Cliente extends Model
{
    protected $table = 'endereco_cliente';

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
        'latitude',
        'longitude',
        'distancia'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
}
