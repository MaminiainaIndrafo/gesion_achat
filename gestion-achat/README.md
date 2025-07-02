# Gestion des Achats Fournisseurs - Outil Interne

## 📝 Description
Outil interne pour la gestion des achats fournisseurs avec intégration Shopify, permettant :
- Import/Export CSV des commandes
- Calcul automatique des marges
- Synchronisation des stocks
- Intégration avec l'API Shopify

## 🛠 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL
- Node.js (pour les assets frontend)

### Configuration
1. Cloner le dépôt :
```bash
git clone [votre-repo-url]
cd gestion-achat


## Package for manipulate CSV
composer require league/csv

## Documentation :
### Commande for create Model Migration Ressource conroller
php artisan make:Model Supplier -mrc
php artisan make:Model Products -mrc
php artisan make:Model Purchases -mrc
php artisan make:Model Stocks -mrc
php artisan make:Model PurchaseCondition  -mrc


### Migration of database
php artisan migrate

### Creat date seeder
php artisan make:seeder DatabaseSeeder

### Execute seeder 
php artisan db:seed


### create commande for import csv using terminal
php artisan make:command ImportPurchases
### Import de commandes fournisseurs au format CSV
php artisan app:import-purchases storage/app/public/donnee_test_gestion_achat --supplier_id=1

### create commande for export csv using terminal
php artisan make:command ExportPurchasesReport
### Export d’un CSV “rapport d’achats” (marges & quantités)
php artisan app:export-purchases-report --date_from=2024-01-01 --date_to=2025-12-31

## Link storage for store file
php artisan storage:link

## Worker Continu
php artisan schedule:work


## Partie 3
### Installation package shopify
composer require shopify/shopify-api