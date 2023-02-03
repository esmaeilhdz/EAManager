<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestProductWarehouse extends Model
{
    use HasFactory;

    protected $hidden = ['request_user_id', 'confirm_user_id', 'created_by', 'updated_at'];
    protected $table = 'request_product_from_warehouses';

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function isConfirm(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }


    public function request_user()
    {
        return $this->hasOne(User::class, 'id', 'request_user_id');
    }

    public function confirm_user()
    {
        return $this->hasOne(User::class, 'id', 'confirm_user_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
