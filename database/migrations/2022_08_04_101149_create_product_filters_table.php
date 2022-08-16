<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->unsignedBigInteger('product_filter_type_id')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('field')->nullable(false);
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();
            $table->foreign('product_filter_type_id')
                ->references('id')
                ->on('product_filter_types')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_filters');
    }
};
