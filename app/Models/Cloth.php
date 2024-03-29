<?php

namespace App\Models;

use App\Exceptions\ApiException;
use App\Traits\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cloth extends Model
{
    use HasFactory, Common;

    protected $primaryKey = 'id';
    protected $hidden = ['id', 'color_id', 'created_by', 'updated_at'];

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
        $company_id = (new Cloth)->getCurrentCompanyOfUser($user);
        static::addGlobalScope('company', function (Builder $builder) use ($company_id) {
            $builder->where('company_id', $company_id);
        });
    }


    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function cloth_buy()
    {
        return $this->hasMany(ClothBuy::class, 'cloth_id', 'id');
    }

    public function cloth_sell()
    {
        return $this->hasMany(ClothSell::class, 'cloth_id', 'id');
    }

    public function color()
    {
        return $this->hasOne(Enumeration::class, 'enum_id', 'color_id')
            ->where('category_name', 'color');
    }

    public function productAccessory()
    {
        return $this->morphOne(ProductAccessory::class, 'model');
    }
}
