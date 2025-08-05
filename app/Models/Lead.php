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
        'num_parcelas',
        'comissao_estipulada',
        'comissao_personalizada',
        'retorno_empresa',
        'num_funcionarios',
        'orcamento_gerado', # bool default false
        'contrato_gerado', # bool default false
        'proximo_contato',
        'recomendador_id'
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

    public function comissao(){
        return $this->hasMany(Comissoes::class, 'lead_id');
    }

    public function indicadoPor(){
        return $this->belongsTo(Recomendadores::class, 'recomendador_id');
    }
}
