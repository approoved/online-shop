<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('product_field_group_id');
            $table->unsignedBigInteger('field_type_id');
            $table->timestamps();

            $table
                ->foreign('product_field_group_id')
                ->references('id')
                ->on('product_field_groups')
                ->cascadeOnDelete();
            $table
                ->foreign('field_type_id')
                ->references('id')
                ->on('field_types')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_fields');
    }
};
