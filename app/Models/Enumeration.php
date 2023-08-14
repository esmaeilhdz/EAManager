<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enumeration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected array $maps = [
        'enum_id' => 'id',
        'enum_caption' => 'caption'
    ];
    protected $hidden = ['enum_id', 'enum_caption'];
    protected $appends = ['id', 'caption'];

    public function getIdAttribute()
    {
        return $this->attributes['enum_id'];
    }
    public function getCaptionAttribute()
    {
        return $this->attributes['enum_caption'];
    }

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
