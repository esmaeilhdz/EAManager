<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePeriod extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function startDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }


    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
