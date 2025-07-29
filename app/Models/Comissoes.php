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
        'status' # enum paga, pendente, cancelada
    ];

    public function vendedor(){
        return $this->belongsTo(Op_Comercial::class, 'vendedor_id');
    }

    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
