<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cliente;
use App\Models\Op_Tecnico;
use App\Models\Status;
use App\Models\User;

use Illuminate\Database\Eloquent\SoftDeletes;

class Documentos_Tecnicos extends Model
{
    protected $table = 'documentos_tecnicos';

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tipo_documento',
        'descricao',
        'data_elaboracao',
        'data_conclusao',
        'cliente_id',
        'status_id',
        'tecnico_id',
        'deleted_by'
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

    public function deletedBy(){
        return $this->belongsTo(User::class, 'deleted_by');
    }

}
