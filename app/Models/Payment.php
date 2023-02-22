<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $hidden = ['model_type', 'model_id', 'company_id', 'account_id', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function paymentDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }


    public function payment()
    {
        return $this->morphTo();
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function gate()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'gate_id')
            ->where('category_name', 'gate');
    }

    public function payment_type()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'payment_type_id')
            ->where('category_name', 'payment_type');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }


}
