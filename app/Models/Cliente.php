<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Telefone;

class Cliente extends Model
{
    protected $table = 'cliente';

    use HasFactory;

    protected $fillable = [
        'nome',
        'cnpj'
    ];

    public function telefone(){
        return $this->hasMany(Telefone::class);
    }
}
