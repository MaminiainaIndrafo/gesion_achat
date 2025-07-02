<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Products;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Products $product): void
    {
        if ($product->isDirty(['purchase_price', 'selling_price', 'shopify_price'])) {
            $product->updateMargin();
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Products $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Products $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Products $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Products $product): void
    {
        //
    }
}
