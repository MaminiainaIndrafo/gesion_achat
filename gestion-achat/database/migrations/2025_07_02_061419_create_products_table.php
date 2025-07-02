<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique(); // Référence fournisseur
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 11, 5); // Prix d'achat HT
            $table->decimal('selling_price', 11, 5)->nullable(); // Prix de vente conseillé
            $table->decimal('shopify_price', 11, 5)->nullable(); // Prix Shopify
            $table->string('shopify_id')->nullable(); // ID du produit dans Shopify
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
