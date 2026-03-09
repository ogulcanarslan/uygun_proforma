<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $fillable = [
        'company_name',
        'auth_name',
        'email',
        'phone',
        'address',
        'city_id'
    ];
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
