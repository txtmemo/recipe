<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipeIdToRecipeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('recipe_id')->after('user_id'); // recipe_idカラムを追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipe_histories', function (Blueprint $table) {
            $table->dropColumn('recipe_id'); // recipe_idカラムを削除
        });
    }
}