# Système de Gestion des Achats Fournisseurs

![Dashboard Preview](docs/dashboard-preview.png) *(Exemple à remplacer par votre capture d'écran)*

## 📋 Table des Matières
- [Fonctionnalités](#-fonctionnalités)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Structure Technique](#-structure-technique)
- [Exemples de Données](#-exemples-de-données)
- [API Endpoints](#-api-endpoints)
- [Tests](#-tests)
- [Déploiement](#-déploiement)
- [License](#-license)

## 🌟 Fonctionnalités

### A. Modélisation & Backend
- **Gestion des fournisseurs** : CRUD complet
- **Import/Export CSV** :
  - Import des commandes fournisseurs
  - Export des rapports d'achats avec marges
- **Calcul automatique** des marges unitaires et globales
- **Gestion des stocks** : Mise à jour automatique

### B. Automatisation
- **Synchronisation API** quotidienne
- **Planification des tâches** via Laravel Scheduler

### C. Intégration Shopify
- Récupération des prix de vente
- Calcul des marges (fournisseur vs Shopify)

### D. Tableau de Bord
- Visualisation des produits/fournisseurs
- Rapports des marges et stocks
- Actions manuelles (import/export, sync)

## 🛠 Installation

### Prérequis
- PHP 8.1+
- Composer 2.0+
- MySQL 5.7+
- Node.js 16+

### Étapes
1. Cloner le dépôt :
```bash
git clone https://github.com/votreuser/gestion-achat.git
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