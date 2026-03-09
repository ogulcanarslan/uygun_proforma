<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $name = 'images';
    protected $fillable = [
        'modul_sayisi_boy',
        'modul_sayisi_en',
        'yukseklik',
        'image',
        'aciklama'
    ];
    protected $casts = [
        'modul_sayisi_boy' => 'double',
        'modul_sayisi_en' => 'double',
        'yukseklik' => 'double',
        'image' => 'string',
        'aciklama' => 'string'

    ];
}
