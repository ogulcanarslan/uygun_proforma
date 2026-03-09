<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function add(Request $request)
    {
        $customer =  Customer::create([
            'auth_name' => Str::ucfirst($request->auth_name) ? Str::ucfirst($request->auth_name) : null,
            'address' => $request->address ? $request->address : null,
            'company_name' => $request->company_name,
            'phone' => $request->phone ? $request->phone : null,
            'city_id'=> $request->city_id ? $request->city_id : null,
        ]);
        if ($customer) {
            return response()->json([
                'status' => true,
                'message' => 'Müşteri başarıyla oluşturuldu!',
                'customer' => $customer
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Müşteri oluşturulurken bir hata oluştu!'
            ], 400);
        }
    }
    public function searchCustomer(Request $request)
    {
        $search = $request->search;
        $customers = Customer::whereRaw("MATCH(company_name) AGAINST (? IN NATURAL LANGUAGE MODE)", [$search])
            ->orWhere('auth_name', 'like', '%' . $search . '%')
            ->orWhere('company_name', 'like', '%' . $search . '%')
            ->get();

        if ($customers && $customers->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'customers' => $customers
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Aradığınız kriterlere uygun müşteri bulunamadı!'
            ], 404);
        }
    }
    public function customers(Request $request)
    {
        $customers = Customer::with('city')->get();
        if ($customers && $customers->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'customers' => $customers
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Kayıtlı müşteri bulunamadı!'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->update([
                'auth_name' => $request->auth_name,
                'address' => $request->address,
                'company_name' => $request->company_name
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Müşteri başarıyla güncellendi!',
                'customer' => $customer
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Müşteri bulunamadı!'
            ], 404);
        }
    }
    public function delete(Request $request, $id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->delete();
            return response()->json([
                'status' => true,
                'message' => 'Müşteri başarıyla silindi!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Müşteri bulunamadı!'
            ], 404);
        }
    }
}
