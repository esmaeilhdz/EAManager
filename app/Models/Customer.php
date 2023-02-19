<?php

namespace App\Models;

use App\Exceptions\ApiException;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory, Common;
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


    /**
     * The "booted" method of the model.
     *
     * @return void
     * @throws ApiException
     */
    protected static function booted()
    {
        $user = Auth::user();
        $company_id = (new Customer)->getCurrentCompanyOfUser($user);
        static::addGlobalScope('company', function (Builder $builder) use ($company_id) {
            $builder->where('company_id', $company_id);
        });
    }


    public function parent()
    {
        return $this->hasOne(Customer::class, 'id', 'parent_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function address()
    {
        return $this->morphMany(Address::class, 'model');
    }
}
