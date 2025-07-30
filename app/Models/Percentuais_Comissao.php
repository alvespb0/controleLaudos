<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Percentuais_Comissao extends Model
{
    protected $table = 'percentuais_comissao';

    use HasFactory;

    protected $fillable = [
        'tipo_cliente',
        'percentual'
    ];
}
