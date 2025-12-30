<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresas_Soc extends Model
{
    protected $table = 'empresas_soc';

    use HasFactory;

    protected $fillable = [
        'nome',
        'codigo_soc',
        'cliente_id'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
