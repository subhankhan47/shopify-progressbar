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
        Schema::create('progress_bar_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->boolean('top_bar_enabled')->default(true)->index();
            $table->boolean('sticky_widget_enabled')->default(true)->index();

            $table->boolean('home_page_show')->default(true)->index();
            $table->boolean('collection_page_show')->default(true)->index();
            $table->boolean('product_page_show')->default(true)->index();

            $table->text('custom_message')->nullable(); // e.g., "You are $X away from..."
            $table->text('completion_message')->nullable(); // e.g., "You are $X away from..."

            $table->boolean('animation_enabled')->default(true)->index();
            $table->string('animation_style')->nullable()->index(); // 'slide', 'fade', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_bar_settings');
    }
};
