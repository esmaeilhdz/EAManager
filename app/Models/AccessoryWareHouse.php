<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryWareHouse extends Model
{
    use HasFactory;

    protected $table = 'accessory_warehouses';

    protected $hidden = ['accessory_id', 'created_at', 'updated_at'];
}
