<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothBuy extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $hidden = ['cloth_id', 'place_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function receiveDate(): Attribute
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

    public function place()
    {
        return $this->hasOne(Place::class, 'id', 'place_id');
    }

}
