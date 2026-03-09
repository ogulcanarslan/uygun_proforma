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
        Schema::create('inputs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_offer_id')->unsigned()->index();
            $table->decimal('modul_sayisi',20,6);
            $table->decimal('modul_sayisi_boy',20,6);
            $table->decimal('modul_sayisi_en',20,6);
            $table->decimal('yukseklik',20,6);
            $table->decimal('taban_saci_mm',20,6)->nullable();
            $table->decimal('bir_kat_modul',20,6)->nullable();
            $table->decimal('bir_bucuk_kat_modul',20,6)->nullable();
            $table->decimal('iki_kat_modul',20,6)->nullable();
            $table->decimal('iki_bucuk_kat_modul',20,6)->nullable();
            $table->decimal('uc_kat_modul',20,6)->nullable();
            $table->decimal('uc_bucuk_kat_modul',20,6)->nullable();
            $table->decimal('dort_kat_modul',20,6)->nullable();
            $table->decimal('tavan_saci_mm',20,6)->nullable();
            $table->foreign('product_offer_id')->references('id')->on('product_offers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inputs');
    }
};
