<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Products;
use App\Models\Purchase;
use App\Models\Purchases;
use App\Models\Stock;
use App\Models\Stocks;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ImportPurchases extends Command
{
    protected $signature = 'app:import-purchases {file : Chemin du fichier CSV} 
                          {--supplier_id= : ID du fournisseur}';

    protected $description = 'Importe les commandes fournisseurs depuis un CSV';

    public function handle()
    {
        $filePath = $this->argument('file');
        $supplierId = $this->option('supplier_id');

        if (!Supplier::find($supplierId)) {
            $this->error("Fournisseur introuvable!");
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        $this->info("Début de l'import...");

        $successCount = 0;
        $errors = [];

        foreach ($csv as $index => $row) {
            $rowNumber = $index + 2;

            $validator = Validator::make($row, [
                'reference_produit' => 'required',
                'nom_produit' => 'required',
                'quantite' => 'required|integer|min:1',
                'prix_unitaire_ht' => 'required|numeric|min:0',
                'numero_facture' => 'nullable',
            ]);

            if ($validator->fails()) {
                $errors[] = "Ligne $rowNumber: " . implode(' ', $validator->errors()->all());
                continue;
            }

            try {
                $product = Products::firstOrCreate(
                    ['reference' => $row['reference_produit']],
                    [
                        'name' => $row['nom_produit'],
                        'purchase_price' => $row['prix_unitaire_ht'],
                        'supplier_id' => $supplierId,
                    ]
                );

                Purchases::create([
                    'product_id' => $product->product_id,
                    'quantity' => $row['quantite'],
                    'unit_price' => $row['prix_unitaire_ht'],
                    'total_price' => $row['quantite'] * $row['prix_unitaire_ht'],
                    'purchase_date' => now(),
                    'invoice_number' => $row['numero_facture'] ?? null,
                ]);

                $stock = Stocks::firstOrNew(['product_id' => $product->product_id]);
                $stock->quantity = ($stock->quantity ?? 0) + $row['quantite'];
                $stock->save();

                if ($product->selling_price || $product->shopify_price) {
                    $this->updateProductMargin($product);
                }

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Ligne $rowNumber: Erreur - " . $e->getMessage();
            }
        }

        $this->info("Import terminé !");
        $this->info("$successCount lignes importées avec succès.");

        if (!empty($errors)) {
            $this->error("Erreurs rencontrées:");
            foreach ($errors as $error) {
                $this->line($error);
            }
        }
    }

    protected function updateProductMargin(Products $product)
    {
        $sellingPrice = $product->shopify_price ?? $product->selling_price;
        $product->margin = $sellingPrice - $product->purchase_price;
        $product->margin_percent = $product->purchase_price > 0 
            ? ($product->margin / $product->purchase_price) * 100 
            : 0;
        $product->save();
    }
}