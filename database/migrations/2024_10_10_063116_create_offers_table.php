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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('title');
            $table->decimal('ham_fiyat',20,6);
            $table->decimal('kdv_haric_fiyat',20,6);
            $table->decimal('kdv_tutari',20,6);
            $table->decimal('toplam_fiyat',20,6);
            $table->decimal('indirim',20,6)->nullable();
            $table->integer('odeme_turu');
            $table->float('kar_orani');
            $table->integer('vade')->nullable();
            $table->boolean('nakliye')->nullable();
            $table->bigInteger('hazirlayan_id')->unsigned();
            $table->foreign('hazirlayan_id')->references('id')->on('hazirlayanlar')->onDelete('cascade');
            $table->string('teslimat_suresi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
