<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enumeration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected function isEnable(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value,
        );
    }

    protected function isEditable(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value,
        );
    }


    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
