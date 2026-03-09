<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'offers';

    protected $fillable = [
        'customer_id',
        'title',
        'ham_fiyat',
        'kdv_haric_fiyat',
        'kdv_tutari',
        'toplam_fiyat',
        'indirim',
        'vade',
        'nakliye',
        'hazirlayan_id',
        'teslimat_suresi',
        'odeme_sekli',
        'odeme_turu',
        "vade_orani",
        "kar_orani"
    ];

    protected $casts = [
        'title' => 'string',
        'ham_fiyat' => 'float',
        'kdv_haric_fiyat' => 'float',
        'kdv_tutari' => 'float',
        'toplam_fiyat' => 'float',
        'indirim' => 'float',
        'vade' => 'int',
        'vade_orani' => 'float',
        'odeme_sekli' => 'string',
        'nakliye' => 'bool',
        'hazirlayan_id' => 'int',
        'customer_id' => 'int',
        'teslimat_suresi' => 'string',
        'odeme_turu' => 'int',
        "kar_orani" => 'float'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }


    public function hazirlayan()
    {
        return $this->belongsTo(Hazirlayan::class, 'hazirlayan_id', 'id');
    }

    public function inputs()
    {
        return $this->hasManyThrough(
            Input::class,
            ProductOffer::class,
            'offer_id',
            'product_offer_id',
            'id',
            'id'
        );
    }


    public function multipliers()
    {
        return $this->hasManyThrough(
            Multiplier::class,
            ProductOffer::class,
            'offer_id',
            'product_offer_id',
            'id',
            'id'
        );
    }


    public function productOffers()
    {
        return $this->hasMany(ProductOffer::class, 'offer_id', 'id');
    }
}
