<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $hidden = ['id', 'parent_id', 'created_by', 'updated_at'];

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


    public function parent()
    {
        return $this->hasOne(Company::class, 'id', 'parent_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}
