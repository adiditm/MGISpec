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
        Schema::table('sliders', function (Blueprint $table) {
            $table->text('desktop_image')->nullable()->change();
            $table->text('mobile_image')->nullable()->change();
            $table->boolean('show_text')->default(false)->change();
            $table->unsignedTinyInteger('overlay')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('desktop_image')->nullable(false)->change();
            $table->string('mobile_image')->nullable()->change();
            $table->boolean('show_text')->default(true)->change();
            $table->unsignedTinyInteger('overlay')->default(40)->change();
        });
    }
};
