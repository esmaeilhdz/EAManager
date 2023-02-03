<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'sender_user_id', 'receiver_user_id', 'notification_level_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function receiverIsRead(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }


    public function sender_user()
    {
        return $this->hasOne(User::class, 'id', 'sender_user_id');
    }

    public function receiver_user()
    {
        return $this->hasOne(User::class, 'id', 'receiver_user_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function notification_level()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'notification_level_id')
            ->where('category_name', 'notification_level');
    }
}
