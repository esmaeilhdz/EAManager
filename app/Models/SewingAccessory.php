<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewingAccessory extends Model
{
    use HasFactory;
    protected $hidden = ['id', 'accessory_id', 'sewing_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    public function accessory()
    {
        return $this->hasOne(Accessory::class, 'id', 'accessory_id');
    }
}
