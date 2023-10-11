<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAccessoryPrice extends Model
{
    use HasFactory;

    protected $hidden = ['updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function product_accessory()
    {
        return $this->hasOne(ProductAccessory::class, 'id', 'product_accessory_id');
    }

    public function product_price()
    {
        return $this->hasOne(ProductPrice::class, 'id', 'product_price_id');
    }
}
