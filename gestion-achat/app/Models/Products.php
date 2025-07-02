<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Products extends Model
{

    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'supplier_id',
        'reference',
        'name',
        'description',
        'purchase_price',
        'selling_price',
        'shopify_price',
        'shopify_id'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchases::class);
    }

    public function stock()
    {
        return $this->hasOne(Stocks::class, 'product_id', 'product_id');
    }
}
