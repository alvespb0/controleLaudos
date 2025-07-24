<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Telefone;

class Cliente extends Model
{
    protected $table = 'cliente';

    use HasFactory;

    protected $fillable = [
        'nome',
        'cnpj',
        'email',
        'cliente_novo'
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
