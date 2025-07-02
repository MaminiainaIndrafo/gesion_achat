<?php

namespace App\Console\Commands;

use App\Models\Products;
use App\Services\ShopifyService;
use Illuminate\Console\Command;

class ShopifySyncCommand extends Command
{
    protected $signature = 'shopify:sync {--all} {--sku=}';
    protected $description = 'Synchronise les données avec Shopify';

    public function handle(ShopifyService $shopify)
    {
        if ($this->option('all')) {
            $this->syncAllProducts($shopify);
        } elseif ($sku = $this->option('sku')) {
            $this->syncSingleProduct($shopify, $sku);
        } else {
            $this->syncRecentProducts($shopify);
        }
    }

    protected function syncAllProducts(ShopifyService $shopify): void
    {
        $products = $shopify->getAllProducts();
        $bar = $this->output->createProgressBar(count($products));

        foreach ($products as $shopifyProduct) {
            foreach ($shopifyProduct['variants'] as $variant) {
                $this->updateLocalProduct($variant);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function syncRecentProducts(ShopifyService $shopify): void
    {
        Products::where('updated_at', '>=', now()->subDays(7))
            ->chunk(100, function($products) use ($shopify) {
                foreach ($products as $product) {
                    if ($variant = $shopify->getProductBySku($product->reference)) {
                        $this->updateProductData($product, $variant);
                    }
                }
            });
    }

    protected function syncSingleProduct(ShopifyService $shopify, string $sku): void
    {
        if ($variant = $shopify->getProductBySku($sku)) {
            $product = Products::where('reference', $sku)->first();
            
            if ($product) {
                $this->updateProductData($product, $variant);
                $this->info("Produit {$sku} mis à jour");
            }
        }
    }

    protected function updateProductData(Products $product, array $variant): void
    {
        $updates = [
            'shopify_id' => $variant['product_id'],
            'shopify_price' => $variant['price'],
            'shopify_updated_at' => now(),
        ];

        if ($product->shopify_price != $variant['price']) {
            $product->update($updates);
            $this->calculateMargin($product);
        }
    }

    protected function calculateMargin(Products $product): void
    {
        if ($product->purchase_price && $product->shopify_price) {
            $margin = (float)$product->shopify_price - (float)$product->purchase_price;
            $marginPercent = ($margin / (float)$product->purchase_price) * 100;

            $product->update([
                'margin' => $margin,
                'margin_percent' => $marginPercent
            ]);
        }
    }
}