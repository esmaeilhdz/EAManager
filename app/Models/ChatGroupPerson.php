<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroupPerson extends Model
{
    use HasFactory;

    protected $hidden = ['person_id', 'chat_group_id', 'created_by', 'updated_at'];
    protected $table = 'chat_group_persons';

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function chat()
    {
        return $this->hasMany(Chat::class, 'chat_group_person_id', 'id');
    }

    public function chat_group()
    {
        return $this->hasOne(ChatGroup::class, 'id', 'chat_group_id');
    }

    public function person()
    {
        return $this->hasOne(Person::class, 'id', 'person_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}
