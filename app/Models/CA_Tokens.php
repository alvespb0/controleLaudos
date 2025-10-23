<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CA_Tokens extends Model
{
    use HasFactory;
    protected $table = 'ca_tokens';

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_at'
    ];
}
