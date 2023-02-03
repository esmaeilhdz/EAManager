<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonCompany extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public function company()
    {
        return $this->hasOne(Company::class);
    }
}
