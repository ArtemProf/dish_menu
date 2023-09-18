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
        Schema::create('product_soc_transformers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_soc_id');
            $table->string('soc_origin', 10);
            $table->unsignedDouble('coefficient');
            $table->string('coefficient_calc', 20);

            $table->foreign('product_soc_id')->references('id')->on('product_socs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_soc_transformers');
    }
};
