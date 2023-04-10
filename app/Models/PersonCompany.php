<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonCompany extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $hidden = ['person_id', 'company_id'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function startWorkDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }

    protected function endWorkDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
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

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
