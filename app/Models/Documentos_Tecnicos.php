<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cliente;
use App\Models\Op_Tecnico;
use App\Models\Status;

class Documentos_Tecnicos extends Model
{
    protected $table = 'documentos_tecnicos';

    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'descricao',
        'data_elaboracao',
        'data_conclusao',
        'cliente_id',
        'status_id',
        'tecnico_id'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico(){
        return $this->belongsTo(Op_Tecnico::class);
    }
    
    public function status(){
        return $this->belongsTo(Status::class);
    }

}
