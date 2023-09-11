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
        Schema::table('recipe_histories', function (Blueprint $table) {
            $table->string('recipe_title')->nullable();
            $table->string('recipe_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_histories', function (Blueprint $table) {
            $table->dropColumn(['recipe_title', 'recipe_image']);
        });
    }
};
