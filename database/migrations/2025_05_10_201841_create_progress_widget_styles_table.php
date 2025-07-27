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
        Schema::create('progress_widget_styles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('position')->default('center-right'); // e.g., top-left, bottom-right
            $table->string('widget_shape')->default('circular'); // e.g., circular, square
            $table->string('bg_color')->default('#ffffff');
            $table->integer('width')->default(60);
            $table->integer('height')->default(60);
            $table->boolean('open_drawer')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_widget_styles');
    }
};
