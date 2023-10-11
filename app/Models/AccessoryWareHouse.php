<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryWareHouse extends Model
{
    use HasFactory;

    protected $table = 'accessory_warehouses';
    protected $hidden = ['accessory_id', 'created_at', 'updated_at'];


    public function accessory()
    {
        return $this->hasOne(Accessory::class, 'id', 'accessory_id');
    }

    public function place()
    {
        return $this->hasOne(Place::class, 'id', 'place_id');
    }
}
