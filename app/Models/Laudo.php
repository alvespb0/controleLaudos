<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Op_Comercial;
use App\Models\Op_Tecnico;
use App\Models\Status;

class Laudo extends Model
{
    protected $table = 'laudos';

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'data_previsao',
        'data_conclusao',
        'data_fim_contrato',
        'data_aceite',
        'esocial',
        'numero_clientes',
        'tecnico_id',
        'status_id',
        'cliente_id',
        'comercial_id',
        'deleted_by',
        'position'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function comercial(){
        return $this->belongsTo(Op_Comercial::class);
    }

    public function tecnico(){
        return $this->belongsTo(Op_Tecnico::class);
    }
    
    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function deletedBy(){
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
