<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\Budget;

class Category extends Model
{
     protected $fillable = ['id', 'name'];

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function budgets(){
        return $this->hasMany(Budget::class);
    }
}
