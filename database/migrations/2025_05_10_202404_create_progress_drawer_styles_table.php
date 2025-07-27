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
        Schema::create('progress_drawer_styles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('filled_progress_color')->default('#00FF00'); // Color of the filled portion
            $table->string('bg_color')->default('#CCCCCC'); // Color of the empty portion
            $table->string('layout')->default('vertical'); // e.g., vertical, horizontal
            $table->string('message_position')->default('below'); // Options: top, bottom
            $table->string('animation')->default('slide_from_right'); // e.g., slide_from_bottom
            $table->string('font_color')->default('#000000');
            $table->integer('font_size')->default(14);
            $table->boolean('show_products_in_bar')->default(true); // Show product of cart in the bar

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_drawer_styles');
    }
};
