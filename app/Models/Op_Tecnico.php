<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Op_Tecnico extends Model
{
    use HasFactory;

    protected $table = 'op_tecnicos';

    protected $fillable = [
        'email',
        'password',
        'usuario'
    ];
}
