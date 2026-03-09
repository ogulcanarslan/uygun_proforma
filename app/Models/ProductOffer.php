<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOffer extends Model
{
    use HasFactory;
    protected $table = 'product_offers';
    protected $fillable = [
        'fiyat',
        'birim_fiyat',
        'depo_ebati',
        'sac_tipi',
        'montaj',
        'urun_id',
        'urun_tonaji',
        'miktar',
        'offer_id',
        'montaj_var_mi'
    ];
    protected $casts = [
        'fiyat' => 'float',
        'birim_fiyat' => 'float',
        'montaj' => 'float',
        'urun_tonaji' => 'float',
        'montaj',
        'montaj_var_mi' => 'boolean'


    ];

    public function input()
    {
        return $this->belongsTo(Input::class, 'id', 'product_offer_id');
    }
    public function multiplier()
    {
        return $this->belongsTo(Multiplier::class, 'id', 'product_offer_id');
    }
}
