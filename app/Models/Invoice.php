<?php

namespace App\Models;

use App\Exceptions\ApiException;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory, Common;
    protected $hidden = ['id', 'customer_id', 'updated_at'];

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
        $company_id = (new Invoice)->getCurrentCompanyOfUser($user);
        static::addGlobalScope('company', function (Builder $builder) use ($company_id) {
            $builder->where('company_id', $company_id);
        });
    }



    public function invoice_products()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id', 'id');
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
