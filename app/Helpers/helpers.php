<?php
namespace App\Helpers;
// Helper fonksiyonları bu dosyada tutacağız
if (!function_exists('getKgToplam')) {
    /**
     * Seçilen ürün tipine göre tüm kg toplamını hesaplar.
     *
     * @param array $label1 Hesaplamaların yapılacağı ana veri dizisi
     * @param string $productType Seçilen ürün tipi (siyah_gal, 304p, 316p)
     * @return float Toplam kg değeri
     */
    function getKgToplam($label1, $productType,$enAltToplam)
    {
        $kgToplam = 0;

        $categories = [
            'bir_kat_modul',
            'bir_bucuk_kat_modul',
            'iki_kat_modul',
            'iki_bucuk_kat_modul',
            'uc_kat_modul',
            'uc_bucuk_kat_modul',
            'dort_kat_modul',
            'taban',
            'tavan'
        ];
        foreach ($categories as $category) {
            if (isset($label1[$category][$productType])) {
                $kgToplam += $label1[$category][$productType]['kg'];
            }
        }

        return $kgToplam+$enAltToplam;
    }
}
