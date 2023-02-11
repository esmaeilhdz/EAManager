<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    use HasFactory;
    protected $hidden = ['id', 'customer_id', 'sale_period_id', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function settlementDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }

    protected function hasReturnPermission(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }

    protected function isCredit(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }

    protected function isComplete(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }


    public function factor_products()
    {
        return $this->hasMany(FactorProduct::class, 'factor_id', 'id');
    }

    public function factor_payments()
    {
        return $this->hasMany(FactorPayment::class, 'factor_id', 'id');
    }

    public function sale_period()
    {
        return $this->hasOne(SalePeriod::class, 'id', 'sale_period_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
