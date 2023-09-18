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
        Schema::create('cook_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('cook_list_id')->index();
            $table->foreignId('dish_id')->index();
            $table->unsignedInteger('amount')->default(1);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('dish_id')->references('id')->on('dishes');

            $table->foreign('cook_list_id')
                  ->references('id')
                  ->on('cook_lists')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cook_list_items');
    }
};
