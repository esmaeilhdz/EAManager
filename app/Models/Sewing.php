<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sewing extends Model
{
    use HasFactory;
    protected $hidden = ['product_id', 'seamstress_person_id', 'place_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function isMozdiDooz(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }


    public function accessories()
    {
        return $this->hasMany(SewingAccessory::class, 'sewing_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function place()
    {
        return $this->hasOne(Place::class, 'id', 'place_id');
    }

    // خیاط
    public function seamstress()
    {
        return $this->hasOne(Person::class, 'id', 'seamstress_person_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}
