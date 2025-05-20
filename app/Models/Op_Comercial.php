<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Op_Comercial extends Model
{
    use HasFactory;

    protected $table = 'op_comercial';

    protected $fillable = [
        'usuario',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function laudos(){
        return $this->hasMany(Laudo::class, 'comercial_id');
    }

}
