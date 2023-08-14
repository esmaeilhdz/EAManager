<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $hidden = ['company_id', 'person_id', 'created_by', 'updated_at'];

    protected function isCheckout(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value,
        );
    }

    protected function fromDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }

    protected function toDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
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


    public function person()
    {
        return $this->hasOne(Person::class, 'id', 'person_id');
    }

    public function salary_deduction()
    {
        return $this->hasMany(SalaryDeduction::class, 'salary_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }


}
