<?php

namespace App\Models;

use App\Exceptions\ApiException;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Factor extends Model
{
    use HasFactory, Common;
    protected $hidden = ['id', 'customer_id', 'sale_period_id', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function settlementDate(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d'),
                'gregorian' => $value
            ],
        );
    }

    protected function hasReturnPermission(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
        );
    }

    protected function isCredit(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value
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
        $company_id = (new Invoice)->getCurrentCompanyOfUser($user);
        static::addGlobalScope('company', function (Builder $builder) use ($company_id) {
            $builder->where('company_id', $company_id);
        });
    }


    public function factor_items()
    {
        return $this->hasMany(FactorItem::class, 'factor_id', 'id');
    }

    public function factor_payments()
    {
        return $this->hasMany(FactorPayment::class, 'factor_id', 'id');
    }

    public function factor_status()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'status')
            ->where('category_name', 'factor_status');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
