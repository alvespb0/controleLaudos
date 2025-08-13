<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comissoes extends Model
{
    protected $table = 'comissoes';

    use HasFactory;

    protected $fillable = [
        'lead_id',
        'vendedor_id',
        'valor_comissao',
        'percentual_aplicado',
        'tipo_comissao',
        'status', # enum paga, pendente, cancelada
        'recomendador_id'
    ];

    public function vendedor(){
        return $this->belongsTo(Op_Comercial::class, 'vendedor_id');
    }

    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function recomendador(){
        return $this->belongsTo(Recomendadores::class, 'recomendador_id');
    }

    public function parcelas(){
        return $this->hasMany(Parcelas_Comissao::class, 'comissao_id');
    }
}
