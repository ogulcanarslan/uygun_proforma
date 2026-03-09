<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multiplier extends Model
{
    use HasFactory;
    protected $table = 'multipliers';
    protected $fillable = [
        'gergi_carpan_a_1',
        'gergi_carpan_a_2',
        'gergi_carpan_b_1',
        'gergi_carpan_b_2',
        'ic_kosebent_a',
        'ic_kosebent_b',
        'ic_kosebent_yuksek',
        'gergi_lamasi_a',
        'gergi_lamasi_b',
        'gergi_lamasi_carpan',
        'product_offer_id'
    ];
}
