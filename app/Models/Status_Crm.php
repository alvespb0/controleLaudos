<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status_Crm extends Model
{
    protected $table = 'status_crm';

    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'position',
        'padrao_sistema'
    ];
}
