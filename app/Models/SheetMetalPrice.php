<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SheetMetalPrice extends Model
{
    use HasFactory;
    protected $table = 'sheet_metal_prices';
    protected $fillable = [
        'title',
        'price',
        'currency',
        'sac_id'
    ];
    public function extraVariables()
    {
        return $this->hasMany(ExtraVariable::class, 'sheet_metal_id');
    }
}
