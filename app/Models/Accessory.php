<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $hidden = ['updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function isEnable(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }

    public function warehouse()
    {
        return $this->hasOne(AccessoryWareHouse::class, 'accessory_id', 'id');
    }

    public function accessoryBuys()
    {
        return $this->hasMany(AccessoryBuy::class, 'accessory_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
