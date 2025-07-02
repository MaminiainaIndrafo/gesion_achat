<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'purchase_date',
        'invoice_number'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
