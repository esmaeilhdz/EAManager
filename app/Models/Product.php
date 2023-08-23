<?php

namespace App\Models;

use App\Exceptions\ApiException;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, Common;

    protected $primaryKey = 'id';
    protected $hidden = ['id', 'cloth_id', 'sale_period_id', 'created_by', 'updated_at'];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => [
                'jalali' => jdate($value)->format('Y/m/d H:i'),
                'gregorian' => $value
            ],
        );
    }

    protected function hasAccessories(): Attribute
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
        $company_id = (new Product)->getCurrentCompanyOfUser($user);
        static::addGlobalScope('company', function (Builder $builder) use ($company_id) {
            $builder->where('company_id', $company_id);
        });
    }


    public function chat()
    {
        return $this->morphMany(Chat::class, 'model');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function cloth()
    {
        return $this->hasOne(Cloth::class, 'id', 'cloth_id');
    }

    public function sale_period()
    {
        return $this->hasOne(SalePeriod::class, 'id', 'sale_period_id');
    }

    public function productWarehouse()
    {
        return $this->hasOne(ProductWarehouse::class, 'product_id', 'id');
    }

    public function productPrice()
    {
        return $this->hasOne(ProductPrice::class, 'product_id', 'id');
    }
}
