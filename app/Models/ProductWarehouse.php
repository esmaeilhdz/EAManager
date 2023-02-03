<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    use HasFactory;
    protected $hidden = ['id', 'cloth_id', 'place_id', 'product_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function place()
    {
        return $this->hasOne(Place::class, 'id', 'place_id');
    }

    public function color()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'color_id')
            ->where('category_name', 'color');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
