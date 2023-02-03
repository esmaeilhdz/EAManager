<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceAttribute extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $hidden = ['id', 'place_id', 'place_attribute_id'];

    public function place()
    {
        return $this->hasOne(Place::class, 'id', 'place_id');
    }

    public function attribute()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'place_attribute_id')
            ->where('category_name', 'place_attribute');
    }
}
