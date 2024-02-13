<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactorItem extends Model
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

    public function discount_type()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'discount_type_id')
            ->where('category_name', 'discount_type');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
