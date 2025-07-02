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
        'shopify_id',
        'margin',
        'margin_percent'
    ];

    // Ajoutez ces méthodes
    public function calculateMargin()
    {
        $sellingPrice = $this->shopify_price ?? $this->selling_price;

        if (!$sellingPrice) return null;

        return [
            'unit' => $sellingPrice - $this->purchase_price,
            'percent' => $this->purchase_price > 0
                ? (($sellingPrice - $this->purchase_price) / $this->purchase_price) * 100
                : null
        ];
    }

    public function updateMargin()
    {
        $margin = $this->calculateMargin();

        if ($margin) {
            $this->update([
                'margin' => $margin['unit'],
                'margin_percent' => $margin['percent']
            ]);
        }

        return $this;
    }

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
