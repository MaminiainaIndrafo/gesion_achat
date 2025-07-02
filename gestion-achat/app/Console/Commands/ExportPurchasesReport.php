<?php

namespace App\Console\Commands;

use App\Models\Purchases;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class ExportPurchasesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-purchases-report
                            {--date_from= : Date de début (format: YYYY-MM-DD)}
                            {--date_to= : Date de fin (format: YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporte un rapport des achats avec marges';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateFrom = $this->option('date_from') ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $this->option('date_to') ?? now()->format('Y-m-d');

        // Récupération des achats
        $purchases = Purchases::with(['product.supplier', 'product.stock'])
            ->whereBetween('purchase_date', [$dateFrom, $dateTo])
            ->get();

        if ($purchases->isEmpty()) {
            $this->error("Aucun achat trouvé pour la période sélectionnée!");
            return;
        }

        // Création du fichier CSV
        $csv = Writer::createFromString('');
        $csv->setDelimiter(';');
        
        // En-têtes
        $csv->insertOne([
            'Date achat',
            'Fournisseur',
            'Référence',
            'Produit',
            'Quantité',
            'Prix unitaire HT',
            'Prix total HT',
            'Prix vente HT',
            'Marge unitaire',
            'Marge totale',
            '% Marge',
            'Stock actuel'
        ]);

        // Données
        foreach ($purchases as $purchase) {
            $sellingPrice = $purchase->product->shopify_price ?? $purchase->product->selling_price;
            $unitMargin = $sellingPrice ? $sellingPrice - $purchase->unit_price : null;
            $totalMargin = $unitMargin ? $unitMargin * $purchase->quantity : null;
            $marginPercent = $purchase->unit_price > 0 && $unitMargin 
                ? ($unitMargin / $purchase->unit_price) * 100 
                : null;

            $csv->insertOne([
                $purchase->purchase_date,
                $purchase->product->supplier->name,
                $purchase->product->reference,
                $purchase->product->name,
                $purchase->quantity,
                number_format($purchase->unit_price, 2, ',', ' '),
                number_format($purchase->total_price, 2, ',', ' '),
                $sellingPrice ? number_format($sellingPrice, 2, ',', ' ') : 'N/A',
                $unitMargin ? number_format($unitMargin, 2, ',', ' ') : 'N/A',
                $totalMargin ? number_format($totalMargin, 2, ',', ' ') : 'N/A',
                $marginPercent ? number_format($marginPercent, 2, ',', ' ') . '%' : 'N/A',
                $purchase->product->stock->quantity ?? 0
            ]);
        }

        // Sauvegarde du fichier
        $filename = "export_achats_{$dateFrom}_au_{$dateTo}.csv";
        Storage::put("exports/{$filename}", $csv->toString());

        $this->info("Export terminé !");
        $this->info("Fichier disponible: storage/app/exports/{$filename}");
    }
}
