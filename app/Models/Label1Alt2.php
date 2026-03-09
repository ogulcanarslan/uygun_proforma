<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label1Alt2 extends Model
{
    use HasFactory;
    protected $table = "label1alt2";
    protected $fillable = [
        'name',
        'price'
    ];
}
