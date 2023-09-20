<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothSellItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $hidden = ['cloth_sell_id', 'color_id', 'updated_at'];
    protected $fillable = ['cloth_sell_id', 'color_id', 'metre', 'unit_price', 'price'];

    public function cloth_sell()
    {
        return $this->hasOne(ClothSell::class, 'id', 'cloth_sell_id');
    }

    public function color()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'color_id')
            ->where('category_name', 'color');
    }
}
