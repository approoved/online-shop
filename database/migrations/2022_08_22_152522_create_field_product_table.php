<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_field_id');
            $table->unsignedBigInteger('product_id');
            $table->string('value');
            $table->timestamps();

            $table
                ->foreign('product_field_id')
                ->references('id')
                ->on('product_fields')
                ->cascadeOnDelete();
            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
