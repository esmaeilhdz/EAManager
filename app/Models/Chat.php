<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $hidden = ['model_type', 'model_id'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function model()
    {
        return $this->morphTo();
    }

    public function chat_group_person()
    {
        return $this->hasOne(ChatGroupPerson::class, 'id', 'chat_group_person_id');
    }
}
