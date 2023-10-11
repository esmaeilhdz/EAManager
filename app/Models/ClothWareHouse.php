<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothWareHouse extends Model
{
    use HasFactory;

    protected $table = 'cloth_warehouses';


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

    public function color()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'color_id')
            ->where('category_name', 'color');
    }
}
