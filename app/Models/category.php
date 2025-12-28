<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Saldo;

class Category extends Model
{
     protected $fillable = ['id', 'name'];

    public function saldos(){
        return $this->hasMany(Saldo::class);
    }
}
