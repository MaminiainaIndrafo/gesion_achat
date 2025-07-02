<?php

namespace App\Console\Commands;

use App\Integrations\SupplierApiClient;
use App\Models\Products;
use Illuminate\Console\Command;

class SyncProductsWithSuppliers extends Command
{
    protected $signature = 'products:sync-suppliers';
    protected $description = 'Synchronise les produits avec les API fournisseurs';

    public function handle(SupplierApiClient $apiClient): int
    {
        Products::query()
            ->with(['supplier', 'stock'])
            ->whereHas('supplier', fn($q) => $q->whereNotNull('api_code'))
            ->chunkById(100, function ($products) use ($apiClient) {
                foreach ($products as $product) {
                    $this->processProduct($product, $apiClient);
                }
            });

        return Command::SUCCESS;
    }

    protected function processProduct(Products $product, SupplierApiClient $apiClient): void
    {
        try {
            $data = $apiClient->getProductData(
                $product->supplier->api_code,
                $product->reference
            );

            if ($data) {
                $this->updateProductData($product, $data);
            }
        } catch (\Exception $e) {
            $this->error("Erreur sur produit {$product->id}: " . $e->getMessage());
        }
    }

    protected function updateProductData(Products $product, array $data): void
    {
        $updates = [];

        if (isset($data['price']) && $data['price'] != $product->purchase_price) {
            $updates['purchase_price'] = $data['price'];
        }

        if (!empty($updates)) {
            $product->update($updates);
            $this->info("Produit {$product->id} mis à jour");
        }
    }
}