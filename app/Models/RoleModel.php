<?php

namespace App\Models;

use App\Facades\RoleFacade;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleModel extends Role
{
    use RoleTrait;

    protected $hidden = ['id', 'updated_at'];

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
     */
    protected static function booted(): void
    {
        static::addGlobalScope('accessRole', function (Builder $builder) {
            $role_ids = RoleFacade::getRolesByUser(Auth::user());
            $builder->whereIn('id', $role_ids);
        });
    }



    public function children()
    {
        return $this->hasMany(RoleModel::class, 'parent_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
