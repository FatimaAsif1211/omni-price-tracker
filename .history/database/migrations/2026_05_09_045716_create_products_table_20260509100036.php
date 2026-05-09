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
            $table->id();
            $table->string('product_name');      // Product ka naam
            $table->string('store_name');        // Daraz, PriceOye, Telemart
            $table->decimal('price', 10, 2);     // Price (e.g., 54999.00)
            $table->string('product_link');      // Original product URL
            $table->string('image_url')->nullable(); // Product image
            $table->timestamp('fetched_at');     // Kab scrape kiya
            $table->timestamps();                 // created_at, updated_at
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
