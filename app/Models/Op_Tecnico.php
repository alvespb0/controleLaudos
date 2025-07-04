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

    public function laudos(){
        return $this->hasMany(Laudo::class, 'tecnico_id');
    }

    public function documentos(){
        return $this->hasMany(Documentos_Tecnicos::class, 'tecnico_id');
    }
}
