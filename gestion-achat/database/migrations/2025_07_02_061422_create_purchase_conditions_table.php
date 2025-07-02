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
        Schema::create('purchase_conditions', function (Blueprint $table) {
            $table->id('condition_id');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('min_amount', 10, 2)->nullable(); // Montant minimum pour remise
            $table->integer('min_quantity')->nullable(); // Quantité minimum pour remise
            $table->decimal('discount_percent', 5, 2)->default(0); // Pourcentage de remise
            $table->string('payment_terms')->default('30 days'); // Conditions de paiement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_conditions');
    }
};
