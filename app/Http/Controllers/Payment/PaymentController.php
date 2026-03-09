<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Label1Alt1;
use App\Models\PaymentExtra;
use Illuminate\Http\Request;
use App\Models\Label1Alt2;

/**
 [['id' => 1, 'price' => 100], ['id' => 2, 'price' => 150]];
 */
class PaymentController extends Controller
{
    public function change(Request $request)
    {
        $tax = $request->tax;
        $vade = $request->vade;

        if ($tax || $vade) {
            $paymentExtra = PaymentExtra::first();
            $paymentExtra->tax = $tax;
            $paymentExtra->vade = $vade;
            $paymentExtra->save();
            return response()->json([
                'status' => true,
                'message' => 'Fiyatlar başarıyla güncellendi'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Fiyatlar güncellenirken hata oluştu!'
            ], 400);
        }
    }

    public function label1(Request $request)
    {
        $data = $request->data;

        if (is_array($data)) {
            foreach ($data as $item) {
                $id = $item['id'];
                $newPrice = $item['price'];


                Label1Alt1::where('id', $id)->update(['price' => $newPrice]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Label1 fiyatları başarıyla güncellendi'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Geçersiz veri formatı'
        ], 400);
    }

    public function label2(Request $request)
    {
        $data = $request->data;

        if (is_array($data)) {
            foreach ($data as $item) {
                $id = $item['id'];
                $newPrice = $item['price'];

                Label1Alt2::where('id', $id)->update(['price' => $newPrice]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Label2 fiyatları başarıyla güncellendi'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Geçersiz veri formatı'
        ], 400);
    }

    public function getLabel1()
    {
        $data = Label1Alt1::all();
        return response()->json([
            'status' => true,
            'message' => 'Fiyatlar başarıyla getirildi',
            'data' => $data
        ], 200);
    }
    public function getLabel2()
    {
        $data = Label1Alt2::all();
        return response()->json([
            'status' => true,
            'message' => 'Fiyatlar başarıyla getirildi',
            'data' => $data
        ], 200);
    }
}
