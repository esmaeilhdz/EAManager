<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothBuyItem extends Model
{
    use HasFactory;

    public function cloth_buy()
    {
        return $this->hasOne(ClothBuy::class, 'id', 'cloth_buy_id');
    }

    public function color()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'color_id')
            ->where('category_name', 'color');
    }
}
