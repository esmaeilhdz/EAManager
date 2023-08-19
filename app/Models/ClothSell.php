<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothSell extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $hidden = ['cloth_id', 'customer_id', 'warehouse_place_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function sellDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }


    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function cloth()
    {
        return $this->hasOne(Cloth::class, 'id', 'cloth_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function warehouse_place()
    {
        return $this->hasOne(Place::class, 'id', 'warehouse_place_id');
    }
}
