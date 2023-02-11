<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactorPayment extends Model
{
    use HasFactory;
    protected $hidden = ['id', 'factor_id', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function payment_type()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'payment_type_id')
            ->where('category_name', 'payment_type');
    }
}
