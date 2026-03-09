<?php

namespace App\Http\Controllers;

use App\Models\Multiplier;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\SheetMetalPrice;
use App\Helpers;
use App\Models\Extra;
use App\Models\Label1Alt1;
use App\Models\Modul;
use App\Models\Label1Alt2;
use function App\Helpers\getKgToplam;
use App\Models\Input;
use App\Models\Product;
use App\Models\Image;
use App\Models\PaymentExtra;
use App\Models\ProductOffer;
use Illuminate\Support\Facades\Log;

class CalculateController extends Controller
{
    public function newCalculate(Request $request)
    {


        // inputlara bak
        $inputs = $request->inputs;
        $vadeOrani = PaymentExtra::select('vade')->value('vade');
        $kdv = PaymentExtra::select('tax')->value('tax');
        $dolarKuru = PaymentExtra::select('dolar_kuru')->value('dolar_kuru');
        $sacFiyatlari = SheetMetalPrice::get();
        $results = [];
        foreach ($inputs as $input) {
            if (isset($input['yeni_birim_fiyat'])) {
                Log::info('Yeni birim fiyat: ' . $input['yeni_birim_fiyat']);
            } else {
                Log::warning('Yeni birim fiyat input içinde yok!');
            }
            $urunAdi = Product::whereIn('id', [$input['urun_id']])->pluck('name');
            $gergiCarpanA1 = $input['modul_sayisi_boy'] - 1;
            $gergiCarpanA2 = ($gergiCarpanA1 * 2) * $input['modul_sayisi_en'];
            $gergiCarpanB1 = $input['modul_sayisi_en'] - 1;
            $gergiCarpanB2 = ($gergiCarpanB1 * 2) * $input['modul_sayisi_boy'];
            $icKosebentA = $input['modul_sayisi_boy'] - 1;
            $icKosebentB = ($input['modul_sayisi_en'] - 1) / 2;
            $gergiLamasiA = $input['modul_sayisi_boy'] + 1;
            $gergiLamasiB = $input['modul_sayisi_en'] + 1;
            $gergiLamasiCarpan = $input['yukseklik'] - 1;
            $icKosebentYuksek = $input['yukseklik'];
            $gergi = $gergiCarpanA2 + $gergiCarpanB2;
            $gergiLamasi = $gergiLamasiA * $gergiLamasiB * $gergiLamasiCarpan;
            $disKoseBent = 4;
            $icKoseBent = $icKosebentA * $icKosebentB * $icKosebentYuksek;
            $depoEbati = ($input['modul_sayisi_boy'] * 1080) . "X" . ($input['modul_sayisi_en'] * 1080) . "X" . ($input['yukseklik'] * 1080);
            $depoTonaji = $input['modul_sayisi_boy'] * 1.08 * $input['modul_sayisi_en'] * 1.08 * $input['yukseklik'] * 1.08;
            $modulSayisi = ($input['modul_sayisi_boy'] * $input['yukseklik'] * 2) + ($input['modul_sayisi_en'] * $input['yukseklik'] * 2);
            $tabanSaci = $input['modul_sayisi_boy'] * $input['modul_sayisi_en'] / 2;
            $tavanSaci = $input['modul_sayisi_boy'] * $input['modul_sayisi_en'] / 2;
            $sacOzellikleri = SheetMetalPrice::where('id', $input['urun_id'])->first();
            $extraData = Extra::with('extraVariables')->get();
            $label1Alt1Prices = Label1Alt1::whereIn('name', ['Conta', 'Civata', 'Montaj', 'Kaide', 'Maşon', 'Merdiven', 'Alt Kapak', 'İşçilik'])
                ->pluck('price', 'name');
            $label1Alt2Prices = Label1Alt2::whereIn('name', ['Conta', 'Civata', 'Montaj', 'Kaide', 'Maşon', 'Merdiven', 'Alt Kapak', 'İşçilik'])
                ->pluck('price', 'name');
            $birKatModulAdet = (float)round(($input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2), 2);
            $birBucukKatAdet =  $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ikiKatModulAdet = $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ikiBucukKatAdet =  $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ucKatAdet = $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ucBucukKatAdet =  $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $dortKatAdet = $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $tabanAdet = $tavanSaci;
            $tavanAdet = ($input['modul_sayisi_boy'] * $input['modul_sayisi_en']) / 2;
            $gergiCivata = ($gergi * 3) + ($gergiLamasi * 2);
            $sacFiyatlari = SheetMetalPrice::get();

            $extras = Extra::with('extraVariables')->get();

            $extraArray = $extras->mapWithKeys(function ($extra) {
                return [
                    $extra->id => [
                        'sac_id' => $extra->extraVariables->mapWithKeys(function ($variable) {
                            return [$variable->sheet_metal_id => $variable->value];
                        })
                    ]
                ];
            });

            $label1 = [];
            foreach ($extraArray as $extraId => $extraData) {
                if ($extraId == 1) {
                    foreach ($extraData['sac_id'] as $sacId => $value) {
                        if (!empty($input['bir_kat_modul'])) {
                            $label1['bir_kat_modul'][$sacId] = ['kg' => $value * $input['bir_kat_modul'] * $birKatModulAdet];
                        }

                        if (!empty($input['bir_bucuk_kat_modul'])) {
                            $label1['bir_bucuk_kat_modul'][$sacId] = ['kg' => $value * $input['bir_bucuk_kat_modul'] * $birBucukKatAdet];
                        }

                        if (!empty($input['iki_kat_modul'])) {
                            $label1['iki_kat_modul'][$sacId] = ['kg' => $value * $input['iki_kat_modul'] * $ikiKatModulAdet];
                        }

                        if (!empty($input['iki_bucuk_kat_modul'])) {
                            $label1['iki_bucuk_kat_modul'][$sacId] = ['kg' => $value * $input['iki_bucuk_kat_modul'] * $ikiBucukKatAdet];
                        }

                        if (!empty($input['uc_kat_modul'])) {
                            $label1['uc_kat_modul'][$sacId] = ['kg' => $value * $input['uc_kat_modul'] * $ucKatAdet];
                        }

                        if (!empty($input['uc_bucuk_kat_modul'])) {
                            $label1['uc_bucuk_kat_modul'][$sacId] = ['kg' => $value * $input['uc_bucuk_kat_modul'] * $ucBucukKatAdet];
                        }

                        if (!empty($input['dort_kat_modul'])) {
                            $label1['dort_kat_modul'][$sacId] = ['kg' => $value * $input['dort_kat_modul'] * $dortKatAdet];
                        }
                    }
                }
                if ($extraId == 2) {
                    foreach ($extraData['sac_id'] as $sacId => $value) {
                        if (!empty($input['taban_saci_mm'])) {
                            $label1['taban'][$sacId] = ['kg' => $value * $input['taban_saci_mm'] * $tabanAdet];
                        }
                        if (!empty($input['tavan_saci_mm'])) {
                            $label1['tavan'][$sacId] = ['kg' => $value * $input['tavan_saci_mm'] * $tavanAdet];
                        }
                    }
                }
            }
            $label1Alt1 = [
                'conta' => ($modulSayisi * 7) * ($label1Alt1Prices['Conta'] ?? 0),
                'civata' => ($modulSayisi * 52 + $gergiCivata)  * ($label1Alt1Prices['Civata'] ?? 0),
                'montaj' => $input['montaj_var_mi'] ?  $modulSayisi * ($label1Alt1Prices['Montaj']) : 0,
                'kaide' => $tabanSaci * 8 * ($label1Alt1Prices['Kaide'] ?? 0),
                'iscilik' => $modulSayisi * ($label1Alt1Prices['İşçilik'] ?? 0),
                'mason' => 1 * $label1Alt1Prices['Maşon'] ?? 0,
                'merdiven' => 1 * $label1Alt1Prices['Merdiven'] ?? 0,
                'alt_kapak' => 1 * $label1Alt1Prices['Alt Kapak'] ?? 0
            ];

            $label1Alt2 = [
                'conta' => $modulSayisi * 7 * ($label1Alt2Prices['Conta'] ?? 0),
                'civata' => ($modulSayisi * 52 + $gergiCivata) * ($label1Alt2Prices['Civata'] ?? 0),
                'montaj' => $input['montaj_var_mi'] ?  $modulSayisi * ($label1Alt2Prices['Montaj']) : 0,
                'kaide' => $tabanSaci * 8 * ($label1Alt2Prices['Kaide'] ?? 0),
                'iscilik' => $modulSayisi * ($label1Alt2Prices['İşçilik'] ?? 0),
                'mason' => 1 * $label1Alt2Prices['Maşon'] ?? 0,
                'merdiven' => 1 * $label1Alt2Prices['Merdiven'] ?? 0,
                'alt_kapak' => 1 * $label1Alt2Prices['Alt Kapak'] ?? 0
            ];
            $label1Alt1Toplam = array_sum($label1Alt1);
            $label1Alt2Toplam = array_sum($label1Alt2);
            $enAltToplam = [
                'gergi' => ($gergi * 0.85),
                'gergi_lamasi' => ($gergiLamasi * 0.45),
                'dis_kosebent' => ($disKoseBent * (0.1 * 1.08 * 7.85 * 2) * $input['yukseklik']),
                'ic_kosebent' => ($icKoseBent * 1.7),
                'toplam' => ($gergi * 0.85) + ($gergiLamasi * 0.45) + ($disKoseBent * (0.1 * 1.08 * 7.85 * 2) * $input['yukseklik']) + ($icKoseBent * 1.7)

            ];

            $enAltToplam = $enAltToplam['toplam'];
            $kgToplam = getKgToplam($label1, $input['urun_id'], $enAltToplam);
            $kgToplamFiyat = $kgToplam * $sacOzellikleri->price;
            $image = Image::where('modul_sayisi_boy', $input['modul_sayisi_boy'])
                ->where('modul_sayisi_en', $input['modul_sayisi_en'])
                ->where('yukseklik', $input['yukseklik'])
                ->first();
            if ($sacOzellikleri->id == 1) {
                $montajFiyat = $label1Alt2['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt2Toplam;
            } else if ($sacOzellikleri->id == 2) {
                $montajFiyat = $label1Alt2['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt2Toplam;
            } else if ($sacOzellikleri->id == 3) {
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt1Toplam;
            } else if ($sacOzellikleri->id == 4) {
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt1Toplam;
            } else if ($sacOzellikleri->id == 5) {
                $siyahKgToplam = getKgToplam($label1, 4, $enAltToplam);
                $siyahSacBirimFiyat =  SheetMetalPrice::whereIn('id', [4])
                    ->pluck('price', 'id')->first();
                $siyacSacToplam = $siyahKgToplam * $siyahSacBirimFiyat;

                $daldirmaFiyat = $siyahKgToplam * $sacOzellikleri->price;
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = ($daldirmaFiyat + $siyacSacToplam + $label1Alt1Toplam);
            } else if ($sacOzellikleri->id == 6) {
                $kgToplam = getKgToplam($label1, 3, $enAltToplam);
                $kgToplamFiyat = $kgToplam * $sacOzellikleri->price;
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = ($kgToplamFiyat + $label1Alt1Toplam) * 1.2;
            }

            if (!empty($input['fiyat_onizleme']) && $input['fiyat_onizleme'] == 1) {
                return response()->json([
                    'status' => true,
                    'message' => "Fiyat önizlemesi başarıyla getirildi!",
                    'fiyat' => ($fiyat * $input['miktar']) * $dolarKuru,
                    'birim_fiyat' => $fiyat * $dolarKuru,
                ], 200);
            }
            if (!empty($input['yeni_birim_fiyat'])) {
                $fiyat = (float)$input['yeni_birim_fiyat']; // TL olarak geldiği için kur çarpımı yapılmaz
            } else {
                $fiyat = $fiyat * $dolarKuru; // Dolar kuru TL'ye çevrilmeli
            }
            $results[] = [
                'birim_fiyat' => $fiyat,
                'fiyat' => ($fiyat * ($input['miktar'])),
                'montaj' => $montajFiyat * $input['miktar'],
                'montaj_var_mi' => $input['montaj_var_mi'] ?? 0,
                'depo_ebati' => $depoEbati,
                'depo_tonaji' => (float)$depoTonaji,
                'sac_tipi' => $urunAdi[0] ?? null,
                'image' => $image ?? null,
                'urun_id' => (int)($input['urun_id'] ?? 0),
                'urun_tonaji' => $depoTonaji,
                'miktar' => (int)($input['miktar'] ?? 0),
                'input' => [
                    'modul_sayisi_boy' => $input['modul_sayisi_boy'] ?? null,
                    'modul_sayisi_en' => $input['modul_sayisi_en'] ?? null,
                    'yukseklik' => $input['yukseklik'] ?? null,
                    'bir_kat_modul' => $input['bir_kat_modul'] ?? null,
                    'bir_bucuk_kat_modul' => $input['bir_bucuk_kat_modul'] ?? null,
                    'iki_kat_modul' => $input['iki_kat_modul'] ?? null,
                    'iki_bucuk_kat_modul' => $input['iki_bucuk_kat_modul'] ?? null,
                    'uc_kat_modul' => $input['uc_kat_modul'] ?? null,
                    'uc_bucuk_kat_modul' => $input['uc_bucuk_kat_modul'] ?? null,
                    'dort_kat_modul' => $input['dort_kat_modul'] ?? null,
                    'tavan_saci_mm' => $input['tavan_saci_mm'] ?? null,
                    'taban_saci_mm' => $input['taban_saci_mm'] ?? null,
                ]
            ];
        }
        $hamToplamFiyat = array_sum(array_column($results, 'fiyat'));


        $karOrani = $request->kar_orani ?? 0; // Varsayılan olarak 0



        $fiyat = $hamToplamFiyat + ($hamToplamFiyat * $karOrani / 100);

        if (isset($request->vade) && $request->odeme_turu == 2) {
            $vadeAyi = $request->vade;
            $fiyat += $fiyat * ($vadeOrani / 100) * $vadeAyi;
        }


        // KDV hariç fiyat
        $kdvHaricFiyat = $fiyat;


        if ($request->son_onizleme == 1) {
            return response()->json([
                'status' => true,
                'fiyat' => $fiyat,
            ]);
        }
        // KDV hesaplama
        $kdvTutar = $fiyat * $kdv / 100;

        // KDV dahil toplam fiyat
        $fiyat += $kdvTutar;
        if ($request->indirim) {
            $fiyat = $fiyat;
            $fiyat = $fiyat - $request->indirim;
        }

        $offer = Offer::create([
            'customer_id' => $request->customer_id,
            'title' => 'Teklif',
            'ham_fiyat' => $hamToplamFiyat,
            'kdv_haric_fiyat' => $kdvHaricFiyat,
            'kdv_tutari' => $kdvTutar,
            'toplam_fiyat' => $fiyat,
            'indirim' => $request->indirim ?? 0,
            'vade' => (int)$request->odeme_turu == 2 ? (int)$request->vade : null,
            'nakliye' => (int) $request->nakliye_var_mi ? true : false,
            'odeme_sekli' => $request->odeme_sekli,
            'hazirlayan_id' => $request->hazirlayan_id,
            'teslimat_suresi' => $request->teslimat_suresi,
            'odeme_turu' => $request->odeme_turu,
            'kar_orani' => $request->kar_orani ?? 0,
            'vade_orani' => $vadeOrani,
            'iskonto_orani' => $iskontoOrani ?? 0,

        ]);
        foreach ($results as $result) {
            $productOffer = ProductOffer::create([
                'fiyat' => $result['fiyat'],
                'birim_fiyat' => $result['birim_fiyat'],
                'depo_ebati' => $result['depo_ebati'],
                'sac_tipi' => $result['sac_tipi'],
                'montaj' => $result['montaj'],
                'urun_id' => $result['urun_id'],
                'urun_tonaji' => $result['urun_tonaji'],
                'miktar' => $result['miktar'],
                'offer_id' => $offer->id,
                'montaj_var_mi' => $result['montaj_var_mi'],
            ]);
            Multiplier::create([
                'product_offer_id' => $productOffer->id,
                'gergi_carpan_a_1' => $gergiCarpanA1,
                'gergi_carpan_a_2' => $gergiCarpanA2,
                'gergi_carpan_b_1' => $gergiCarpanB1,
                'gergi_carpan_b_2' => $gergiCarpanB2,
                'ic_kosebent_a' => $icKosebentA,
                'ic_kosebent_b' => $icKosebentB,
                'ic_kosebent_yuksek' => $icKosebentYuksek,
                'gergi_lamasi_a' => $gergiLamasiA,
                'gergi_lamasi_b' => $gergiLamasiB,
                'gergi_lamasi_carpan' => $gergiLamasiCarpan,
            ]);
            Input::create([
                'product_offer_id' => $productOffer->id,
                'modul_sayisi' => $modulSayisi,
                'modul_sayisi_boy' => $result['input']['modul_sayisi_boy'],
                'modul_sayisi_en' => $result['input']['modul_sayisi_en'],
                'yukseklik' => $result['input']['yukseklik'],
                'taban_saci_mm' => $result['input']['taban_saci_mm'],
                'bir_kat_modul' => $result['input']['bir_kat_modul'],
                'bir_bucuk_kat_modul' => $result['input']['bir_bucuk_kat_modul'],
                'iki_kat_modul' => $result['input']['iki_kat_modul'],
                'iki_bucuk_kat_modul' => $result['input']['iki_bucuk_kat_modul'],
                'uc_kat_modul' => $result['input']['uc_kat_modul'],
                'uc_bucuk_kat_modul' => $result['input']['uc_bucuk_kat_modul'],
                'dort_kat_modul' => $result['input']['dort_kat_modul'],
                'tavan_saci_mm' => $result['input']['tavan_saci_mm']
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Teklif başarıyla oluşturuldu!',
            'data' => $results,
            'ham_fiyat' => $hamToplamFiyat,
            'kdv_haric_fiyat' => $kdvHaricFiyat,
            'kdv_tutari' => $kdvTutar,
            'toplam_fiyat' => $fiyat,
            'indirim' => $request->indirim,
            'vade' => (int)$request->odeme_turu == 2 ? (int)$request->vade : null,
            'nakliye' => (int) $request->nakliye_var_mi ? true : false,
            'hazirlayan_id' => $request->hazirlayan_id,
            'teslimat_suresi' => $request->teslimat_suresi,
            'vade_orani' => $vadeOrani,
            'odeme_sekli' => $request->odeme_sekli,
            'odeme_turu' => $request->odeme_turu,

        ], 200);
    }

    public function newUpdate(Request $request)
    {


        // inputlara bak
        $inputs = $request->inputs;
        $vadeOrani = PaymentExtra::select('vade')->value('vade');
        $kdv = PaymentExtra::select('tax')->value('tax');
        $dolarKuru = PaymentExtra::select('dolar_kuru')->value('dolar_kuru');
        $offerId = (int)$request->teklif_id;
        $oldOffer = Offer::find($offerId);
        $oldOffer->delete();
        $sacFiyatlari = SheetMetalPrice::get();
        $results = [];
        foreach ($inputs as $input) {
            if (isset($input['yeni_birim_fiyat'])) {
                Log::info('Yeni birim fiyat: ' . $input['yeni_birim_fiyat']);
            } else {
                Log::warning('Yeni birim fiyat input içinde yok!');
            }


            $urunAdi = Product::whereIn('id', [$input['urun_id']])->pluck('name');
            $gergiCarpanA1 = $input['modul_sayisi_boy'] - 1;
            $gergiCarpanA2 = ($gergiCarpanA1 * 2) * $input['modul_sayisi_en'];
            $gergiCarpanB1 = $input['modul_sayisi_en'] - 1;
            $gergiCarpanB2 = ($gergiCarpanB1 * 2) * $input['modul_sayisi_boy'];
            $icKosebentA = $input['modul_sayisi_boy'] - 1;
            $icKosebentB = ($input['modul_sayisi_en'] - 1) / 2;
            $gergiLamasiA = $input['modul_sayisi_boy'] + 1;
            $gergiLamasiB = $input['modul_sayisi_en'] + 1;
            $gergiLamasiCarpan = $input['yukseklik'] - 1;
            $icKosebentYuksek = $input['yukseklik'];
            $gergi = $gergiCarpanA2 + $gergiCarpanB2;
            $gergiLamasi = $gergiLamasiA * $gergiLamasiB * $gergiLamasiCarpan;
            $disKoseBent = 4;
            $icKoseBent = $icKosebentA * $icKosebentB * $icKosebentYuksek;
            $depoEbati = ($input['modul_sayisi_boy'] * 1080) . "X" . ($input['modul_sayisi_en'] * 1080) . "X" . ($input['yukseklik'] * 1080);
            $depoTonaji = $input['modul_sayisi_boy'] * 1.08 * $input['modul_sayisi_en'] * 1.08 * $input['yukseklik'] * 1.08;
            $modulSayisi = ($input['modul_sayisi_boy'] * $input['yukseklik'] * 2) + ($input['modul_sayisi_en'] * $input['yukseklik'] * 2);
            $tabanSaci = $input['modul_sayisi_boy'] * $input['modul_sayisi_en'] / 2;
            $tavanSaci = $input['modul_sayisi_boy'] * $input['modul_sayisi_en'] / 2;
            $sacOzellikleri = SheetMetalPrice::where('id', $input['urun_id'])->first();
            $extraData = Extra::with('extraVariables')->get();
            $label1Alt1Prices = Label1Alt1::whereIn('name', ['Conta', 'Civata', 'Montaj', 'Kaide', 'Maşon', 'Merdiven', 'Alt Kapak', 'İşçilik'])
                ->pluck('price', 'name');
            $label1Alt2Prices = Label1Alt2::whereIn('name', ['Conta', 'Civata', 'Montaj', 'Kaide', 'Maşon', 'Merdiven', 'Alt Kapak', 'İşçilik'])
                ->pluck('price', 'name');
            $birKatModulAdet = (float)round(($input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2), 2);
            $birBucukKatAdet =  $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ikiKatModulAdet = $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ikiBucukKatAdet =  $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ucKatAdet = $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $ucBucukKatAdet =  $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $dortKatAdet = $input['modul_sayisi_boy'] * 2 + $input['modul_sayisi_en'] * 2;
            $tabanAdet = $tavanSaci;
            $tavanAdet = ($input['modul_sayisi_boy'] * $input['modul_sayisi_en']) / 2;
            $gergiCivata = ($gergi * 3) + ($gergiLamasi * 2);
            $sacFiyatlari = SheetMetalPrice::get();

            $extras = Extra::with('extraVariables')->get();

            $extraArray = $extras->mapWithKeys(function ($extra) {
                return [
                    $extra->id => [
                        'sac_id' => $extra->extraVariables->mapWithKeys(function ($variable) {
                            return [$variable->sheet_metal_id => $variable->value];
                        })
                    ]
                ];
            });

            $label1 = [];
            foreach ($extraArray as $extraId => $extraData) {
                if ($extraId == 1) {
                    foreach ($extraData['sac_id'] as $sacId => $value) {
                        if (!empty($input['bir_kat_modul'])) {
                            $label1['bir_kat_modul'][$sacId] = ['kg' => $value * $input['bir_kat_modul'] * $birKatModulAdet];
                        }

                        if (!empty($input['bir_bucuk_kat_modul'])) {
                            $label1['bir_bucuk_kat_modul'][$sacId] = ['kg' => $value * $input['bir_bucuk_kat_modul'] * $birBucukKatAdet];
                        }

                        if (!empty($input['iki_kat_modul'])) {
                            $label1['iki_kat_modul'][$sacId] = ['kg' => $value * $input['iki_kat_modul'] * $ikiKatModulAdet];
                        }

                        if (!empty($input['iki_bucuk_kat_modul'])) {
                            $label1['iki_bucuk_kat_modul'][$sacId] = ['kg' => $value * $input['iki_bucuk_kat_modul'] * $ikiBucukKatAdet];
                        }

                        if (!empty($input['uc_kat_modul'])) {
                            $label1['uc_kat_modul'][$sacId] = ['kg' => $value * $input['uc_kat_modul'] * $ucKatAdet];
                        }

                        if (!empty($input['uc_bucuk_kat_modul'])) {
                            $label1['uc_bucuk_kat_modul'][$sacId] = ['kg' => $value * $input['uc_bucuk_kat_modul'] * $ucBucukKatAdet];
                        }

                        if (!empty($input['dort_kat_modul'])) {
                            $label1['dort_kat_modul'][$sacId] = ['kg' => $value * $input['dort_kat_modul'] * $dortKatAdet];
                        }
                    }
                }
                if ($extraId == 2) {
                    foreach ($extraData['sac_id'] as $sacId => $value) {
                        if (!empty($input['taban_saci_mm'])) {
                            $label1['taban'][$sacId] = ['kg' => $value * $input['taban_saci_mm'] * $tabanAdet];
                        }
                        if (!empty($input['tavan_saci_mm'])) {
                            $label1['tavan'][$sacId] = ['kg' => $value * $input['tavan_saci_mm'] * $tavanAdet];
                        }
                    }
                }
            }
            $label1Alt1 = [
                'conta' => ($modulSayisi * 7) * ($label1Alt1Prices['Conta'] ?? 0),
                'civata' => ($modulSayisi * 52 + $gergiCivata)  * ($label1Alt1Prices['Civata'] ?? 0),
                'montaj' => $input['montaj_var_mi'] ?  $modulSayisi * ($label1Alt1Prices['Montaj']) : 0,
                'kaide' => $tabanSaci * 8 * ($label1Alt1Prices['Kaide'] ?? 0),
                'iscilik' => $modulSayisi * ($label1Alt1Prices['İşçilik'] ?? 0),
                'mason' => 1 * $label1Alt1Prices['Maşon'] ?? 0,
                'merdiven' => 1 * $label1Alt1Prices['Merdiven'] ?? 0,
                'alt_kapak' => 1 * $label1Alt1Prices['Alt Kapak'] ?? 0
            ];

            $label1Alt2 = [
                'conta' => $modulSayisi * 7 * ($label1Alt2Prices['Conta'] ?? 0),
                'civata' => ($modulSayisi * 52 + $gergiCivata) * ($label1Alt2Prices['Civata'] ?? 0),
                'montaj' => $input['montaj_var_mi'] ?  $modulSayisi * ($label1Alt2Prices['Montaj']) : 0,
                'kaide' => $tabanSaci * 8 * ($label1Alt2Prices['Kaide'] ?? 0),
                'iscilik' => $modulSayisi * ($label1Alt2Prices['İşçilik'] ?? 0),
                'mason' => 1 * $label1Alt2Prices['Maşon'] ?? 0,
                'merdiven' => 1 * $label1Alt2Prices['Merdiven'] ?? 0,
                'alt_kapak' => 1 * $label1Alt2Prices['Alt Kapak'] ?? 0
            ];
            $label1Alt1Toplam = array_sum($label1Alt1);
            $label1Alt2Toplam = array_sum($label1Alt2);
            $enAltToplam = [
                'gergi' => ($gergi * 0.85),
                'gergi_lamasi' => ($gergiLamasi * 0.45),
                'dis_kosebent' => ($disKoseBent * (0.1 * 1.08 * 7.85 * 2) * $input['yukseklik']),
                'ic_kosebent' => ($icKoseBent * 1.7),
                'toplam' => ($gergi * 0.85) + ($gergiLamasi * 0.45) + ($disKoseBent * (0.1 * 1.08 * 7.85 * 2) * $input['yukseklik']) + ($icKoseBent * 1.7)

            ];

            $enAltToplam = $enAltToplam['toplam'];
            $kgToplam = getKgToplam($label1, $input['urun_id'], $enAltToplam);
            $kgToplamFiyat = $kgToplam * $sacOzellikleri->price;
            $image = Image::where('modul_sayisi_boy', $input['modul_sayisi_boy'])
                ->where('modul_sayisi_en', $input['modul_sayisi_en'])
                ->where('yukseklik', $input['yukseklik'])
                ->first();
            if ($sacOzellikleri->id == 1) {
                $montajFiyat = $label1Alt2['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt2Toplam;
            } else if ($sacOzellikleri->id == 2) {
                $montajFiyat = $label1Alt2['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt2Toplam;
            } else if ($sacOzellikleri->id == 3) {
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt1Toplam;
            } else if ($sacOzellikleri->id == 4) {
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = $kgToplamFiyat + $label1Alt1Toplam;
            } else if ($sacOzellikleri->id == 5) {
                $siyahKgToplam = getKgToplam($label1, 4, $enAltToplam);
                $siyahSacBirimFiyat =  SheetMetalPrice::whereIn('id', [4])
                    ->pluck('price', 'id')->first();
                $siyacSacToplam = $siyahKgToplam * $siyahSacBirimFiyat;

                $daldirmaFiyat = $siyahKgToplam * $sacOzellikleri->price;
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = ($daldirmaFiyat + $siyacSacToplam + $label1Alt1Toplam);
            } else if ($sacOzellikleri->id == 6) {
                $kgToplam = getKgToplam($label1, 3, $enAltToplam);
                $kgToplamFiyat = $kgToplam * $sacOzellikleri->price;
                $montajFiyat = $label1Alt1['montaj'];
                $fiyat = ($kgToplamFiyat + $label1Alt1Toplam) * 1.2;
            }

            if (!empty($input['fiyat_onizleme']) && $input['fiyat_onizleme'] == 1) {
                return response()->json([
                    'status' => true,
                    'message' => "Fiyat önizlemesi başarıyla getirildi!",
                    'fiyat' => ($fiyat * $input['miktar']) * $dolarKuru,
                    'birim_fiyat' => $fiyat * $dolarKuru,
                ], 200);
            }
            if (!empty($input['yeni_birim_fiyat'])) {
                $fiyat = (float)$input['yeni_birim_fiyat']; // TL olarak geldiği için kur çarpımı yapılmaz
            } else {
                $fiyat = $fiyat * $dolarKuru; // Dolar kuru TL'ye çevrilmeli
            }
            $results[] = [
                'birim_fiyat' => $fiyat,
                'fiyat' => ($fiyat * ($input['miktar'])),
                'montaj' => $montajFiyat * $input['miktar'],
                'montaj_var_mi' => $input['montaj_var_mi'] ?? 0,
                'depo_ebati' => $depoEbati,
                'depo_tonaji' => (float)$depoTonaji,
                'sac_tipi' => $urunAdi[0] ?? null,
                'image' => $image ?? null,
                'urun_id' => (int)($input['urun_id'] ?? 0),
                'urun_tonaji' => $depoTonaji,
                'miktar' => (int)($input['miktar'] ?? 0),
                'input' => [
                    'modul_sayisi_boy' => $input['modul_sayisi_boy'] ?? null,
                    'modul_sayisi_en' => $input['modul_sayisi_en'] ?? null,
                    'yukseklik' => $input['yukseklik'] ?? null,
                    'bir_kat_modul' => $input['bir_kat_modul'] ?? null,
                    'bir_bucuk_kat_modul' => $input['bir_bucuk_kat_modul'] ?? null,
                    'iki_kat_modul' => $input['iki_kat_modul'] ?? null,
                    'iki_bucuk_kat_modul' => $input['iki_bucuk_kat_modul'] ?? null,
                    'uc_kat_modul' => $input['uc_kat_modul'] ?? null,
                    'uc_bucuk_kat_modul' => $input['uc_bucuk_kat_modul'] ?? null,
                    'dort_kat_modul' => $input['dort_kat_modul'] ?? null,
                    'tavan_saci_mm' => $input['tavan_saci_mm'] ?? null,
                    'taban_saci_mm' => $input['taban_saci_mm'] ?? null,
                ]
            ];
        }
        $hamToplamFiyat = array_sum(array_column($results, 'fiyat'));


        $karOrani = $request->kar_orani ?? 0; // Varsayılan olarak 0



        $fiyat = $hamToplamFiyat + ($hamToplamFiyat * $karOrani / 100);

        if (isset($request->vade) && $request->odeme_turu == 2) {
            $vadeAyi = $request->vade;
            $fiyat += $fiyat * ($vadeOrani / 100) * $vadeAyi;
        }


        // KDV hariç fiyat
        $kdvHaricFiyat = $fiyat;


        if ($request->son_onizleme == 1) {
            return response()->json([
                'status' => true,
                'fiyat' => $fiyat,
            ]);
        }
        // KDV hesaplama
        $kdvTutar = $fiyat * $kdv / 100;

        // KDV dahil toplam fiyat
        $fiyat += $kdvTutar;
        if ($request->indirim) {
            $fiyat = $fiyat;
            $fiyat = $fiyat - $request->indirim;
        }

        $offer = Offer::create([
            'customer_id' => $request->customer_id,
            'title' => 'Teklif',
            'ham_fiyat' => $hamToplamFiyat,
            'kdv_haric_fiyat' => $kdvHaricFiyat,
            'kdv_tutari' => $kdvTutar,
            'toplam_fiyat' => $fiyat,
            'indirim' => $request->indirim ?? 0,
            'vade' => (int)$request->odeme_turu == 2 ? (int)$request->vade : null,
            'nakliye' => (int) $request->nakliye_var_mi ? true : false,
            'odeme_sekli' => $request->odeme_sekli,
            'hazirlayan_id' => $request->hazirlayan_id,
            'teslimat_suresi' => $request->teslimat_suresi,
            'odeme_turu' => $request->odeme_turu,
            'kar_orani' => $request->kar_orani ?? 0,
            'vade_orani' => $vadeOrani,
            'iskonto_orani' => $iskontoOrani ?? 0,

        ]);
        foreach ($results as $result) {
            $productOffer = ProductOffer::create([
                'fiyat' => $result['fiyat'],
                'birim_fiyat' => $result['birim_fiyat'],
                'depo_ebati' => $result['depo_ebati'],
                'sac_tipi' => $result['sac_tipi'],
                'montaj' => $result['montaj'],
                'urun_id' => $result['urun_id'],
                'urun_tonaji' => $result['urun_tonaji'],
                'miktar' => $result['miktar'],
                'offer_id' => $offer->id,
                'montaj_var_mi' => $result['montaj_var_mi'],
            ]);
            Multiplier::create([
                'product_offer_id' => $productOffer->id,
                'gergi_carpan_a_1' => $gergiCarpanA1,
                'gergi_carpan_a_2' => $gergiCarpanA2,
                'gergi_carpan_b_1' => $gergiCarpanB1,
                'gergi_carpan_b_2' => $gergiCarpanB2,
                'ic_kosebent_a' => $icKosebentA,
                'ic_kosebent_b' => $icKosebentB,
                'ic_kosebent_yuksek' => $icKosebentYuksek,
                'gergi_lamasi_a' => $gergiLamasiA,
                'gergi_lamasi_b' => $gergiLamasiB,
                'gergi_lamasi_carpan' => $gergiLamasiCarpan,
            ]);
            Input::create([
                'product_offer_id' => $productOffer->id,
                'modul_sayisi' => $modulSayisi,
                'modul_sayisi_boy' => $result['input']['modul_sayisi_boy'],
                'modul_sayisi_en' => $result['input']['modul_sayisi_en'],
                'yukseklik' => $result['input']['yukseklik'],
                'taban_saci_mm' => $result['input']['taban_saci_mm'],
                'bir_kat_modul' => $result['input']['bir_kat_modul'],
                'bir_bucuk_kat_modul' => $result['input']['bir_bucuk_kat_modul'],
                'iki_kat_modul' => $result['input']['iki_kat_modul'],
                'iki_bucuk_kat_modul' => $result['input']['iki_bucuk_kat_modul'],
                'uc_kat_modul' => $result['input']['uc_kat_modul'],
                'uc_bucuk_kat_modul' => $result['input']['uc_bucuk_kat_modul'],
                'dort_kat_modul' => $result['input']['dort_kat_modul'],
                'tavan_saci_mm' => $result['input']['tavan_saci_mm']
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Teklif başarıyla güncellendi!',
            'data' => $results,
            'ham_fiyat' => $hamToplamFiyat,
            'kdv_haric_fiyat' => $kdvHaricFiyat,
            'kdv_tutari' => $kdvTutar,
            'toplam_fiyat' => $fiyat,
            'indirim' => $request->indirim,
            'vade' => (int)$request->odeme_turu == 2 ? (int)$request->vade : null,
            'nakliye' => (int) $request->nakliye_var_mi ? true : false,
            'hazirlayan_id' => $request->hazirlayan_id,
            'teslimat_suresi' => $request->teslimat_suresi,
            'vade_orani' => $vadeOrani,
            'odeme_sekli' => $request->odeme_sekli,
            'odeme_turu' => $request->odeme_turu,

        ], 200);
    }


    public function offers(Request $request)
    {
        $offers = Offer::with('customer', 'hazirlayan')->get();
        return response()->json([
            'status' => 'true',
            'message' => 'Teklifler başarıyla listelendi',
            'offers' => $offers,
        ]);
    }
    public function offerById(Request $request, $id)
    {
        $offer = Offer::with('customer', 'hazirlayan', 'productOffers', 'productOffers.multiplier')->find($id);
        $extras = PaymentExtra::pluck('vade');
        $offer->payment_extras = $extras->first();

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Teklif bulunamadı.',
            ], 404);
        }


        foreach ($offer->productOffers as $productOffer) {
            if ($productOffer->input) {
                $image = Image::where('modul_sayisi_boy', $productOffer->input->modul_sayisi_boy)
                    ->where('modul_sayisi_en', $productOffer->input->modul_sayisi_en)
                    ->where('yukseklik', $productOffer->input->yukseklik)
                    ->first();

                $productOffer->image = $image;
                $productOffer->modul_sayisi_boy = $productOffer->input->modul_sayisi_boy;
                $productOffer->modul_sayisi_en = $productOffer->input->modul_sayisi_en;
                $productOffer->yukseklik = $productOffer->input->yukseklik;
                $productOffer->bir_kat_modul = $productOffer->input->bir_kat_modul ?? null;
                $productOffer->bir_bucuk_kat_modul = $productOffer->input->bir_bucuk_kat_modul ?? null;
                $productOffer->iki_kat_modul = $productOffer->input->iki_kat_modul ?? null;
                $productOffer->iki_bucuk_kat_modul = $productOffer->input->iki_bucuk_kat_modul ?? null;
                $productOffer->uc_kat_modul = $productOffer->input->uc_kat_modul ?? null;
                $productOffer->uc_bucuk_kat_modul = $productOffer->input->uc_bucuk_kat_modul ?? null;
                $productOffer->dort_kat_modul = $productOffer->input->dort_kat_modul ?? null;
                $productOffer->taban_saci_mm = $productOffer->input->taban_saci_mm ?? null;
                $productOffer->tavan_saci_mm = $productOffer->input->tavan_saci_mm ?? null;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Teklif başarıyla getirildi',
            'offer' => $offer,
        ]);
    }
}
