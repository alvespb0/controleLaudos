<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Op_Comercial extends Model
{
    use HasFactory;

    protected $table = 'op_comercial';

    protected $fillable = [
        'email',
        'password',
        'usuario'
    ];
}
