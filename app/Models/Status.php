<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    use HasFactory;

    protected $fillable = [
        'nome',
        'cor',
        'position'
    ];

    public function laudos(){
        return $this->hasMany(Laudo::class);
    }

    public function documentos(){
        return $this->hasMany(Documentos_Tecnicos::class, 'status_id');
    }
}
