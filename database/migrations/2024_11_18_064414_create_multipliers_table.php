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
        Schema::create('multipliers', function (Blueprint $table) {
            $table->id();
            $table->decimal('gergi_carpan_a_1',20,6);
            $table->decimal('gergi_carpan_a_2',20,6);
            $table->decimal('gergi_carpan_b_1',20,6);
            $table->decimal('gergi_carpan_b_2',20,6);
            $table->decimal('ic_kosebent_a',20,6);
            $table->decimal('ic_kosebent_b',20,6);
            $table->decimal('ic_kosebent_yuksek',20,6);
            $table->decimal('gergi_lamasi_a',20,6);
            $table->decimal('gergi_lamasi_b',20,6);
            $table->decimal('gergi_lamasi_carpan',20,6);
            $table->bigInteger('product_offer_id')->unsigned();
            $table->foreign('product_offer_id')->references('id')->on('product_offers')->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multipliers');
    }
};
