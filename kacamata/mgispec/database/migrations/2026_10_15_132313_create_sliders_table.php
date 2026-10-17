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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->json('badge')->nullable();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->json('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->json('cta2_text')->nullable();
            $table->string('cta2_url')->nullable();
            $table->json('desktop_image')->nullable();
            $table->json('mobile_image')->nullable();
            $table->boolean('show_text')->default(false);
            $table->string('text_position', 10)->default('left');
            $table->unsignedTinyInteger('overlay')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
