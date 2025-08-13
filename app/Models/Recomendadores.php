<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomendadores extends Model
{
    protected $table = 'recomendadores';

    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf'
    ];
}
