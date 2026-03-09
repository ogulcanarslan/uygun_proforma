<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraVariable extends Model
{
    use HasFactory;
    protected $table = "extras_variables";
    public function extra()
    {
        return $this->belongsTo(Extra::class, 'extra_id');
    }

    /**
     * Her ExtraVariable bir SheetMetal'le ilişkilidir.
     */
    public function sheetMetal()
    {
        return $this->belongsTo(SheetMetalPrice::class, 'sheet_metal_id');
    }
}
