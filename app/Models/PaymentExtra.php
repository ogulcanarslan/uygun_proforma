<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentExtra extends Model
{
    use HasFactory;
    protected $table = 'payments_extras';
    protected $fillable = ['tax', 'vade','dolar_kuru'];
}
