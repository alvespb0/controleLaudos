<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Telefone;

class Cliente extends Model
{
    protected $table = 'cliente';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'cnpj',
        'email',
        'tipo_cliente'
    ];

    public function telefone(){
        return $this->hasMany(Telefone::class);
    }

    public function endereco(){
        return $this->hasOne(Endereco_Cliente::class);
    }

    public function dadosCobranca(){
        return $this->hasOne(Dados_Cobranca::class);
    }
}
