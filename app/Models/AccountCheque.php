<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCheque extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $hidden = ['id', 'account_id', 'updated_at'];

    protected function isEnable(): Attribute
    {
        return Attribute::get(
            get: fn ($value) => (bool) $value,
        );
    }
}
