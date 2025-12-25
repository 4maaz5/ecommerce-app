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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->unique()->nullable();

            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->integer('capacity')->nullable(); // in pallets / CBM

            // Store image reference (from uploads table or media table)
            $table->string('image_path')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
