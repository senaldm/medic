<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable=[
        'batch_no',
        'name',
        'seller_company',
        'buying_price',
        'sell_price',
        'quantity',

    ];


   
    
}
