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
        Schema::create('extras_variables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('extra_id')->unsigned();
            $table->string('name')->nullable();
            $table->bigInteger('sheet_metal_id')->unsigned();
            $table->decimal('value',20,6);
            $table->foreign('extra_id')->references('id')->on('extras')->onDelete('cascade');
            $table->foreign('sheet_metal_id')->references('id')->on('sheet_metal_prices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extras_variables');
    }
};
