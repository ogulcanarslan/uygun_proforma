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
        Schema::create('product_offers', function (Blueprint $table) {
            $table->id();
            $table->decimal('fiyat',20,6);
            $table->decimal('birim_fiyat',20,6);
            $table->string('depo_ebati');
            $table->string('sac_tipi');
            $table->decimal('montaj',20,6);
            $table->bigInteger('urun_id')->unsigned();
            $table->foreign('urun_id')->references('id')->on('products')->onDelete('cascade');
            $table->decimal('urun_tonaji',20,6);
            $table->integer('miktar');
            $table->integer('montaj_var_mi');
            $table->bigInteger('offer_id')->unsigned();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_offers');
    }
};
