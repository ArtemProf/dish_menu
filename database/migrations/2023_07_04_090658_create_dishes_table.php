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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dish_category_id')->index();
            $table->string('title')->index();
            $table->string('video');
            $table->string('tag');
            $table->string('image');
            $table->text('description');
            $table->string('nutritional_value');
            $table->text('exclaim');
            $table->timestamps();

            $table->foreign('dish_category_id')->references('id')
                  ->on('dish_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
