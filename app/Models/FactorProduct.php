<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactorProduct extends Model
{
    use HasFactory;
    protected $hidden = ['factor_id', 'product_warehouse_id', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function factor()
    {
        return $this->hasOne(Factor::class, 'id', 'factor_id');
    }

    public function product_warehouse()
    {
        return $this->hasOne(ProductWarehouse::class, 'id', 'product_warehouse_id');
    }
}
