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
        'cor'
    ];

    public function laudos(){
        return $this->hasMany(Laudo::class);
    }

}
