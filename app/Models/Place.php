<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $hidden = ['place_kind_id', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function fromDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }


    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function place_kind()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'place_kind_id')
            ->where('category_name', 'place_kind');
    }

    public function place_attribute()
    {
        return $this->hasMany(PlaceAttribute::class, 'place_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}
