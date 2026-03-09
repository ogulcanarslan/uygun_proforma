<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SheetMetalPrice;

class SheetMetalPriceController extends Controller
{
    public function prices()
    {
        $prices = SheetMetalPrice::all();
        if ($prices && $prices->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Sac fiyatları başarıyla listelendi!',
                'prices' => $prices
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Sac fiyatları bulunamadı!'
            ], 404);
        }
    }
    public function update(Request $request)
{
    $data = $request->data;

    if (is_array($data)) {
        foreach ($data as $item) {
            $id = $item['id'];
            $newPrice = $item['price'];

            SheetMetalPrice::where('id', $id)->update(['price' => $newPrice]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Fiyatlar başarıyla güncellendi'
        ], 200);
    }

    // Hatalı veri durumunda yanıt
    return response()->json([
        'status' => false,
        'message' => 'Geçersiz veri formatı'
    ], 400);
}

}
