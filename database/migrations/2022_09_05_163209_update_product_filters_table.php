<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_filters', function (Blueprint $table) {
            $table->dropColumn('field');

            $table->unsignedBigInteger('product_field_id');

            $table->foreign('product_field_id')
                ->references('id')
                ->on('product_fields')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_filters', function (Blueprint $table) {
            $table->dropColumn('product_field_id');

            $table->string('field');
        });
    }
};
