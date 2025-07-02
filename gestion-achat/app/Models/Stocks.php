<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $table = 'stocks';

    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'product_id',
        'quantity',
        'alert_threshold'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
