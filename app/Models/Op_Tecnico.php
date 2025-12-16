<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Op_Tecnico extends Model
{
    use HasFactory;

    protected $table = 'op_tecnicos';

    protected $fillable = [
        'usuario',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function documentos(){
        return $this->hasMany(Documentos_Tecnicos::class, 'tecnico_id');
    }

    public function responsavel(){
        return $this->hasMany(Responsaveis::class, 'tecnico_id');
    }

    public function levantamento(){
        return $this->responsavel()->where('tipo', 'levantamento');

    }

    public function engenheiro(){
        return $this->responsavel()->where('tipo', 'engenheiro');
    }

    public function digitacao(){
        return $this->responsavel()->where('tipo', 'digitacao');
    }
}
