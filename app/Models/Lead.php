<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';

    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'vendedor_id',
        'status_id',
        'observacoes',
        'nome_contato',
        'valor_min_sugerido',
        'valor_max_sugerido',
        'valor_definido',
        'num_funcionarios',
        'orcamento_gerado', # bool default false
        'contrato_assinado', # bool default false
        'notificado',
        'proximo_contato'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(){
        return $this->belongsTo(Op_Comercial::class, 'vendedor_id');
    }

    public function status(){
        return $this->belongsTo(Status_Crm::class, 'status_id');
    }
}
