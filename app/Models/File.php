<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Laudo;
use App\Models\User;
use App\Models\Cliente;

class File extends Model
{
    protected $table = 'files';

    use HasFactory;

    protected $fillable = [
        'nome_arquivo',
        'tipo',
        'caminho',
        'data_referencia',
        'cliente_id',
        'laudo_id',
        'criado_por'
    ];

    public function laudo(){
        return $this->belongsTo(Laudo::class);
    }

    public function autor(){
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
}
