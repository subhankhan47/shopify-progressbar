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
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('amount', 10, 2)->index();
            $table->enum('reward_type', ['free_shipping', 'free_product'])->index();

            $table->string('product_id')->nullable()->index();
            $table->unsignedInteger('priority')->default(0)->index();
            $table->boolean('auto_add_product')->default(false)->index();
            $table->json('shipping_regions')->nullable(); // only for free_shipping, e.g., ["US", "CA"]

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
