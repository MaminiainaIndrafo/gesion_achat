<?php

namespace Database\Seeders;

use App\Models\Products;
use App\Models\PurchaseCondition;
use App\Models\Purchases;
use App\Models\Stocks;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création d'un fournisseur
        $supplier = Supplier::create([
            'name' => 'Fournisseur Test',
            'contact_email' => 'contact@fournisseur-test.com',
            'contact_phone' => '0123456789',
            'address' => '123 Rue du Test, 75000 Paris',
        ]);

        // Condition d'achat pour ce fournisseur
        PurchaseCondition::create([
            'supplier_id' => $supplier->supplier_id,
            'min_amount' => 1000,
            'discount_percent' => 5,
            'payment_terms' => '30 jours fin de mois'
        ]);

        // Création de produits
        $products = [
            [
                'reference' => 'PROD001',
                'name' => 'Produit Test 1',
                'purchase_price' => 50.00,
                'selling_price' => 75.00
            ],
            [
                'reference' => 'PROD002',
                'name' => 'Produit Test 2',
                'purchase_price' => 30.00,
                'selling_price' => 45.00
            ]
        ];

        foreach ($products as $productData) {
            $product = Products::create(array_merge($productData, [
                'supplier_id' => $supplier->supplier_id
            ]));

            // Création du stock
            Stocks::create([
                'product_id' => $product->product_id,
                'quantity' => 100,
                'alert_threshold' => 10
            ]);

            // Création d'achats
            Purchases::create([
                'product_id' => $product->product_id,
                'quantity' => 50,
                'unit_price' => $product->purchase_price,
                'purchase_date' => now()->subDays(30),
                'invoice_number' => 'FAC-' . now()->year . '-001'
            ]);
        }
    }
}
