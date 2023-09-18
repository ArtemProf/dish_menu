<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dish_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->references('id')->on('dishes')->index();
            $table->unsignedBigInteger('dish_ingredient_category_id')->index();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('type_id')->nullable()->index();
            $table->string('title');
            $table->string('comment')->nullable();
            $table->float('amount')->default(1);
            $table->string('amount_soc')->nullable();
            $table->string('amount_origin');
            $table->string('not_required')->default(false);

            $table->foreign('dish_ingredient_category_id')->references('id')->on('dish_ingredient_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_ingredients');
    }
};
