<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hazirlayan extends Model
{
    use HasFactory;
    protected $table = 'hazirlayanlar';
    protected $fillable = [
        'auth_name',
        'phone',
        'email',
    ];
}
