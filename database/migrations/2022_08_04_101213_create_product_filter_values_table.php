<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        //TODO nullable unsigned

        Schema::create('product_filter_values', function (Blueprint $table) {
            $table->id();
            $table->integer('product_filter_id');
            $table->string('value');
            $table->json('search_value');
            $table->timestamps();

            $table->foreign('product_filter_id')
                ->references('id')
                ->on('product_filters')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_filter_values');
    }
};
