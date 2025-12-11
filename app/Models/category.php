<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\Budget;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $guarded = [''];

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function budgets(){
        return $this->hasMany(Budget::class);
    }
}

