<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsaveis extends Model
{
    protected $table = 'responsaveis';

    use HasFactory;

    protected $fillable = [
        'laudo_id',
        'tecnico_id',
        'tipo' #enum
    ];

}
