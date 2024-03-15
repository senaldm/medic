<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDrugDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'drug_no',
        'drug_name',
        'quantity',
        'purchase_date'
    ];
}
