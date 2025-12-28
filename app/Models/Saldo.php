<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;    

class Saldo extends Model
{
    /** @use HasFactory<\Database\Factories\SaldoFactory> */
    use HasFactory;
    
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    
}
