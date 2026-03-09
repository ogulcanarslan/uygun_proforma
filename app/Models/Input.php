<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;
    protected $table = "inputs";
    protected $fillable = [
        'product_offer_id',
        'modul_sayisi',
        'modul_sayisi_boy',
        'modul_sayisi_en',
        'yukseklik',
        'taban_saci_mm',
        'bir_kat_modul',
        'bir_bucuk_kat_modul',
        'iki_kat_modul',
        'iki_bucuk_kat_modul',
        'uc_kat_modul',
        'uc_bucuk_kat_modul',
        'dort_kat_modul',
        'tavan_saci_mm',
    ];
    protected $casts = [
        'product_offer_id' => 'integer',
        'modul_sayisi' => 'float',
        'modul_sayisi_boy' => 'float',
        'modul_sayisi_en' => 'float',
        'yukseklik' => 'float',
        'taban_saci_mm' => 'float',
        'bir_kat_modul' => 'float',
        'bir_bucuk_kat_modul' => 'float',
        'iki_kat_modul' => 'float',
        'iki_bucuk_kat_modul' => 'float',
        'uc_kat_modul' => 'float',
        'uc_bucuk_kat_modul' => 'float',
        'dort_kat_modul' => 'float',
        'tavan_saci_mm' => 'float',
    ];


}
