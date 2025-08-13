<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcelas_Comissao extends Model
{
    protected $table = 'parcelas_comissao';

    use HasFactory;

    protected $fillable = [
        'comissao_id',
        'numero_parcela',
        'valor_parcela',
        'data_prevista',
        'status' #enum
    ];

    public function comissao(){
        return $this->belongsTo(Comissoes::class, 'comissao_id');
    }
}
