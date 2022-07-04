<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('parent_id')
                ->unsigned()
                ->nullable();
            $table->integer('position', false, true);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
