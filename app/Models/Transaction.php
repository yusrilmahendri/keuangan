<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\category;
use App\Models\TransactionItem;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $guarded = [''];
    protected $casts = [
    'transaction_date' => 'date',
];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function items(){
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }
}
