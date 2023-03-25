<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    protected $hidden = ['model_type', 'model_id', 'original_file_name', 'updated_at'];

    public function attachment()
    {
        return $this->morphTo();
    }

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }


    public function attachment_type()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'attachment_type_id')
            ->where('category_name', 'attachment_type');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
