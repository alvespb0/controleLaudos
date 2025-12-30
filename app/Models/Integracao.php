<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integracao extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'integracoes';

    /**
     * Campos permitidos para mass assignment
     */
    protected $fillable = [
        'sistema',
        'descricao',
        'endpoint',
        'slug',
        'username',
        'password_enc',
        'auth', # enum basic, bearer, wss
        'tipo', # enum soap, rest
    ];

}
