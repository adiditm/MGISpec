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
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku', 150)->unique();
            $table->string('slug', 150)->unique();
            $table->json('name');
            $table->string('badge')->nullable();
            $table->string('badge_color', 20)->default('pink');
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->string('thumbnail');
            $table->json('gallery')->nullable();
            $table->json('highlights')->nullable();
            $table->json('specs')->nullable();
            $table->json('technologies')->nullable();
            $table->boolean('is_featured')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
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
