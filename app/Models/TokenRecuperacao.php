<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenRecuperacao extends Model
{
    protected $table = 'password_reset_tokens';

    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'expiracao'
    ];
}
