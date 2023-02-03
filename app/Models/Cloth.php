<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cloth extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $hidden = ['id', 'color_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function color()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'color_id')
            ->where('category_name', 'color');
    }
}
