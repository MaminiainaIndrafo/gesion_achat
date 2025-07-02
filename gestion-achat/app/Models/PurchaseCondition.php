<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCondition extends Model
{
    use HasFactory;

    protected $table = 'purchase_conditions';

    protected $primaryKey = 'condition_id';

    protected $fillable = [
        'supplier_id', 'min_amount', 'min_quantity',
        'discount_percent', 'payment_terms'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
